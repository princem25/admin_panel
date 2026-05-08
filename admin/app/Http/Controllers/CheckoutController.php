<?php

namespace App\Http\Controllers;

use App\Events\Admin\OrderPlaced;
use App\Models\Order;
use App\Models\OrderItem;
use App\Services\CartService;
use App\Mail\OrderConfirmation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use App\Notifications\ProductLowStock;
use App\Models\User;
use Illuminate\Support\Facades\Cache;

class CheckoutController extends Controller
{
    protected CartService $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    /**
     * Show the checkout form.
     */
    public function index()
    {
        $summary = $this->cartService->getCartSummary();

        if ($summary['items']->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty.');
        }

        return view('checkout.index', [
            'cartItems'    => $summary['items'],
            'grandTotal'   => $summary['grandTotal'],
            'totalSavings' => $summary['totalSavings'],
        ]);
    }

    /**
     * Store a new order.
     */
    public function store(Request $request)
    {
        $request->validate([
            'full_name'        => 'required|string|max:255',
            'phone'            => 'required|string|max:20',
            'shipping_address' => 'required|string',
            'payment_method'   => 'required|in:cod,card,upi',
            'notes'            => 'nullable|string',
        ]);

        $summary = $this->cartService->getCartSummary();
        $cartItems = $summary['items'];

        if ($cartItems->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Cannot place an order with an empty cart.');
        }

        try {
            $order = DB::transaction(function () use ($request, $summary, $cartItems) {
                // 1. Create Order
                $order = tap(Order::create([
                    'user_id'          => auth()->id(),
                    'total_amount'     => $summary['grandTotal'],
                    'payment_method'   => $request->payment_method,
                    'status'           => 'pending',
                    'full_name'        => $request->full_name,
                    'phone'            => $request->phone,
                    'shipping_address' => $request->shipping_address,
                    'notes'            => $request->notes,
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

            Log::info('Order placed successfully', ['order_id' => $order->id, 'user_id' => auth()->id()]);

            // NOTE: OrderPlaced event is dispatched automatically by OrderObserver after commit

            try {
                // Delay for 30 seconds to allow background invoice generation to complete before attaching
                Mail::to($order->user->email)->later(now()->addSeconds(15), new OrderConfirmation($order));
            } catch (\Exception $mailException) {
                Log::error('Failed to queue order confirmation email', [
                    'order_id' => $order->id,
                    'error'    => $mailException->getMessage(),
                ]);
            }

            return redirect()->route('checkout.success', $order->id)->with('success', 'Your order has been placed successfully! 🎉');
        } catch (\Exception $e) {
            Log::error('Order placement failed', [
                'user_id' => auth()->id(),
                'error'   => $e->getMessage()
            ]);

            return back()->withInput()->with('error', 'Something went wrong while placing your order. Please try again.');
        }
    }

    /**
     * Show order success page.
     */
    public function success(Order $order)
    {
        // Ensure the order belongs to the authenticated user
        if ($order->user_id !== auth()->id()) {
            abort(403);
        }

        return view('checkout.success', compact('order'));
    }
}
