<?php

namespace App\Http\Controllers;

use App\Events\Admin\OrderPlaced;
use App\Models\Order;
use App\Models\OrderItem;
use App\Services\CartService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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
                $order = Order::create([
                    'user_id'          => auth()->id(),
                    'total_amount'     => $summary['grandTotal'],
                    'payment_method'   => $request->payment_method,
                    'status'           => 'pending',
                    'full_name'        => $request->full_name,
                    'phone'            => $request->phone,
                    'shipping_address' => $request->shipping_address,
                    'notes'            => $request->notes,
                ]);

                // 2. Create Order Items
                foreach ($cartItems as $item) {
                    OrderItem::create([
                        'order_id'   => $order->id,
                        'product_id' => $item->product_id,
                        'quantity'   => $item->quantity,
                        'price'      => $item->unit_price,
                    ]);
                }

                // 3. Complete Order (Clear Cart without restoring stock)
                $this->cartService->completeOrder();

                return $order;
            });

            Log::info('Order placed successfully', ['order_id' => $order->id, 'user_id' => auth()->id()]);

            // Load items and user for the broadcast payload
            $order->load(['items', 'user']);
            event(new OrderPlaced($order));

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
