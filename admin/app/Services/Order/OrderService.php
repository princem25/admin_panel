<?php

namespace App\Services\Order;

use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class OrderService
{
    /**
     * Get orders based on user role.
     *
     * @param User $user
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getOrdersForUser($user)
    {
        if ($user->role === 'admin') {
            return Order::with('user')->latest()->paginate(12);
        }

        return $user->orders()->latest()->paginate(12);
    }

    /**
     * Cancel a pending order and restore stock.
     *
     * @param Order $order
     * @throws \Exception
     */
    public function cancelOrder(Order $order)
    {
        // Note: State check is kept in the controller to preserve specific error handling behavior.

        DB::transaction(function () use ($order) {
            // Update order status to cancelled
            $order->update(['status' => 'cancelled']);

            // Eager load items and products to avoid N+1
            $order->load('items.product');

            // Restore product stock for each item
            foreach ($order->items as $item) {
                if ($item->product) {
                    $item->product->increment('stock', $item->quantity);
                }
            }
        });
    }
}
