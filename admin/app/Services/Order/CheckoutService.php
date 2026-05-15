<?php

namespace App\Services\Order;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use App\Models\Product;
use App\Notifications\ProductLowStock;
use App\Services\CartService;
use App\Mail\OrderConfirmation;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Cache;

class CheckoutService
{
    protected CartService $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    /**
     * Place a new order.
     *
     * @param array $data
     * @param User $user
     * @return Order
     * @throws \Exception
     */
    public function placeOrder(array $data, $user)
    {
        $summary = $this->cartService->getCartSummary();
        $cartItems = $summary['items'];
        $lowStockProducts = [];

        // 🚀 Refactored for Exercise 40.2: Atomic transaction with 3 retry attempts
        $order = DB::transaction(function () use ($data, $summary, $cartItems, $user, &$lowStockProducts) {
            // 1. Create Order
            $order = Order::create([
                'user_id'          => $user->id,
                'total_amount'     => $summary['grandTotal'],
                'payment_method'   => $data['payment_method'],
                'status'           => 'pending',
                'full_name'        => $data['full_name'],
                'phone'            => $data['phone'],
                'shipping_address' => $data['shipping_address'],
                'notes'            => $data['notes'] ?? null,
            ]);

            Log::channel('orders')->info('New checkout order created inside transaction', ['id' => $order->id]);

            // 2. Create Order Items & Decrement Stock (Moved here from CartService)
            foreach ($cartItems as $item) {
                // 🔒 Row locking to prevent overselling
                $product = Product::where('id', $item->product_id)->lockForUpdate()->firstOrFail();

                if ($product->stock < $item->quantity) {
                    throw new \Exception("Sorry, '{$product->name}' is no longer available in the requested quantity.");
                }

                OrderItem::create([
                    'order_id'   => $order->id,
                    'product_id' => $item->product_id,
                    'quantity'   => $item->quantity,
                    'price'      => $item->unit_price,
                ]);

                // Stock decrement happens here now
                $product->decrement('stock', $item->quantity);

                if ($product->fresh()->stock <= 10) {
                    $lowStockProducts[] = $product;
                }
            }

            // 3. Complete Order (Clear Cart)
            $this->cartService->completeOrder();

            return $order;
        }, 3); // 🔄 3 retry attempts for deadlocks

        // 🚀 Side effects moved OUTSIDE transaction closure
        
        // 1. Notifications
        foreach ($lowStockProducts as $product) {
            $cacheKey = "low_stock_alert_{$product->id}";
            Cache::remember($cacheKey, 600, function () use ($product, $order) {
                $admins = User::where('role', 'admin')->get();
                rescue(function () use ($admins, $product, $order) {
                    Notification::send($admins, new ProductLowStock($product, $order->id));
                });
                return true;
            });
        }

        // 2. Emails
        try {
            Mail::to($order->user->email)->later(now()->addSeconds(15), new OrderConfirmation($order, $order->user));
        } catch (\Exception $mailException) {
            Log::error('Failed to queue order confirmation email', [
                'order_id' => $order->id,
                'error'    => $mailException->getMessage(),
            ]);
        }

        return $order;
    }
}
