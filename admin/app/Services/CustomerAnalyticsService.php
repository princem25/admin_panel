<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class CustomerAnalyticsService
{
    /**
     * Get summary statistics for a customer
     */
    public function getCustomerStats(int $userId): array
    {
        try {
            $orders = Order::where('user_id', $userId)->get();

            return [
                'total_orders'        => $orders->count(),
                'total_spent'         => $orders->where('status', '!=', 'cancelled')->sum('total_amount'),
                'average_order_value' => $orders->where('status', '!=', 'cancelled')->avg('total_amount') ?? 0,
            ];
        } catch (\Exception $e) {
            Log::error('Customer order analytics error', [
                'message' => $e->getMessage(),
                'trace'   => $e->getTraceAsString()
            ]);
            return [
                'total_orders'        => 0,
                'total_spent'         => 0,
                'average_order_value' => 0,
            ];
        }
    }

    /**
     * Get Top 3 most ordered products for a customer
     */
    public function getTopProducts(int $userId): Collection
    {
        try {
            $orderIds = Order::where('user_id', $userId)
                ->where('status', '!=', 'cancelled')
                ->pluck('id');

            $items = OrderItem::with('product')
                ->whereIn('order_id', $orderIds)
                ->get();

            return $items->groupBy('product_id')
                ->map(function ($group) {
                    $firstItem = $group->first();
                    return [
                        'product_id'   => $firstItem->product_id,
                        'product_name' => $firstItem->product ? $firstItem->product->name : 'Unknown Product',
                        'total_quantity' => $group->sum('quantity'),
                    ];
                })
                ->sortByDesc('total_quantity')
                ->take(3)
                ->values();
        } catch (\Exception $e) {
            Log::error('Customer order analytics error', [
                'message' => $e->getMessage(),
                'trace'   => $e->getTraceAsString()
            ]);
            return collect();
        }
    }

    /**
     * Get orders grouped by status for a customer
     */
    public function getOrdersByStatus(int $userId): Collection
    {
        try {
            $orders = Order::where('user_id', $userId)->get();

            return $orders->groupBy('status')
                ->map(function ($group, $status) {
                    return [
                        'status'       => $status,
                        'total_orders' => $group->count(),
                        'total_amount' => $group->sum('total_amount'),
                    ];
                })
                ->sortByDesc('total_amount')
                ->values();
        } catch (\Exception $e) {
            Log::error('Customer order analytics error', [
                'message' => $e->getMessage(),
                'trace'   => $e->getTraceAsString()
            ]);
            return collect();
        }
    }
}
