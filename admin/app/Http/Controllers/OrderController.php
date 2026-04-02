<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    /**
     * Display a listing of orders.
     */
    public function index()
    {
        $user = auth()->user();

        if ($user->role === 'admin') {
            // Admin sees all orders
            $orders = Order::with('user')->latest()->paginate(10);
        } else {
            // Regular user only sees their own orders
            $orders = $user->orders()->latest()->paginate(10);
        }

        return view('orders.index', compact('orders'));
    }

    /**
     * Display the specified order.
     */
    public function show(Order $order)
    {
        // Authorization check
        if (auth()->user()->role !== 'admin' && $order->user_id !== auth()->id()) {
            abort(403);
        }

        // Load items related to this order
        $order->load(['items.product', 'user']);

        return view('orders.show', compact('order'));
    }

    /**
     * Cancel a pending order and restore product stock.
     */
    public function cancel(Order $order)
    {
        // 1. Authorization check
        if (auth()->user()->role !== 'admin' && $order->user_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        // 2. Only allow cancellation for pending orders
        if ($order->status !== 'pending') {
            return back()->with('error', 'Only pending orders can be cancelled.');
        }

        try {
            DB::transaction(function () use ($order) {
                // 3. Update order status to cancelled
                $order->update(['status' => 'cancelled']);

                // 4. Restore product stock for each item
                foreach ($order->items as $item) {
                    if ($item->product) {
                        $item->product->increment('stock', $item->quantity);
                    }
                }
            });

            return back()->with('success', 'Order #' . $order->id . ' has been cancelled and stock has been restored.');

        } catch (\Exception $e) {
            return back()->with('error', 'Failed to cancel the order. Please try again.');
        }
    }
}
