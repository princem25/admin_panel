<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\Order\OrderService;
use Illuminate\Support\Facades\URL;

class OrderController extends Controller
{
    protected OrderService $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    /**
     * Display a listing of orders.
     */
    public function index()
    {
        $orders = $this->orderService->getOrdersForUser(auth()->user());

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

        $downloadUrl = URL::temporarySignedRoute(
            'invoices.download', now()->addMinutes(10), ['order' => $order->id]
        );

        return view('orders.show', compact('order', 'downloadUrl'));
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
            $this->orderService->cancelOrder($order);

            return back()->with('success', 'Order #' . $order->id . ' has been cancelled and stock has been restored.');

        } catch (\Exception $e) {
            return back()->with('error', 'Failed to cancel the order. Please try again.');
        }
    }
}
