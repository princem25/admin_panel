<?php

namespace App\Services\Order;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
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

        // Note: Empty cart check is kept in the controller to preserve specific redirect behavior.

        $order = DB::transaction(function () use ($data, $summary, $cartItems, $user) {
            // 1. Create Order
            $order = tap(Order::create([
                'user_id'          => $user->id,
                'total_amount'     => $summary['grandTotal'],
                'payment_method'   => $data['payment_method'],
                'status'           => 'pending',
                'full_name'        => $data['full_name'],
                'phone'            => $data['phone'],
                'shipping_address' => $data['shipping_address'],
                'notes'            => $data['notes'] ?? null,
            ]), function ($o) {
                Log::channel('orders')->info('New checkout order', ['id' => $o->id]);
            });

            // 2. Create Order Items
            foreach ($cartItems as $item) {
                OrderItem::create([
                    'order_id'   => $order->id,
                    'product_id' => $item->product_id,
                    'quantity'   => $item->quantity,
                    'price'      => $item->unit_price,
                ]);

                // Trigger Low Stock Notification if applicable
                $product = $item->product;
                $product->refresh();
                if ($product->stock <= 10) {
                    $cacheKey = "low_stock_alert_{$product->id}";
                    
                    Cache::remember($cacheKey, 600, function () use ($product, $order) {
                        $admins = User::where('role', 'admin')->get();
                        rescue(function () use ($admins, $product, $order) {
                            Notification::send($admins, new ProductLowStock($product, $order->id));
                        });
                        return true; // Mark as sent in cache
                    });
                }
            }

            // 3. Complete Order (Clear Cart without restoring stock)
            $this->cartService->completeOrder();

            return $order;
        });

        Log::info('Order placed successfully', ['order_id' => $order->id, 'user_id' => $user->id]);

        try {
            // Delay for 15 seconds to allow background invoice generation to complete before attaching
            Mail::to($order->user->email)->later(now()->addSeconds(15), new OrderConfirmation($order));
        } catch (\Exception $mailException) {
            Log::error('Failed to queue order confirmation email', [
                'order_id' => $order->id,
                'error'    => $mailException->getMessage(),
            ]);
        }

        return $order;
    }
}
