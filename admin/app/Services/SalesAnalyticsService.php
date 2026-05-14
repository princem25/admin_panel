<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class SalesAnalyticsService
{
    /**
     * Get Monthly Sales Report
     * Refactored for Exercise 40.2: Uses raw SQL with parameter bindings.
     */
    public function getMonthlySales($userId = null): Collection
    {
        try {
            $sql = "SELECT DATE_FORMAT(created_at, '%Y-%m') as month, 
                           SUM(total_amount) as total_revenue, 
                           AVG(total_amount) as average_order_value, 
                           COUNT(*) as total_orders 
                    FROM orders 
                    WHERE status NOT IN ('cancelled', 'pending')";

            $params = [];

            if ($userId) {
                $sql .= " AND user_id = :user_id";
                $params['user_id'] = $userId;
            }

            $sql .= " GROUP BY month ORDER BY month DESC";

            $results = DB::select($sql, $params);

            // Convert to collection of arrays for consistency with previous return type
            return collect($results)->map(function ($row) {
                return [
                    'month'               => $row->month,
                    'total_revenue'       => (float) $row->total_revenue,
                    'average_order_value' => (float) $row->average_order_value,
                    'total_orders'        => (int) $row->total_orders,
                ];
            });
        } catch (\Exception $e) {
            Log::error('Sales analytics error', [
                'message' => $e->getMessage(),
                'trace'   => $e->getTraceAsString()
            ]);
            return collect();
        }
    }

    /**
     * Get Top 10 Products
     * Based on total quantity sold
     */
    public function getTopProducts(): Collection
    {
        try {
            $items = OrderItem::with('product')
                ->whereHas('order', function ($query) {
                    $query->genuine();
                })->get();

            return $items->groupBy('product_id')
                ->map(function ($group) {
                    $firstItem = $group->first();
                    return [
                        'product_id'         => $firstItem->product_id,
                        'product_name'       => $firstItem->product ? $firstItem->product->name : 'Unknown',
                        'total_quantity_sold' => $group->sum('quantity'),
                    ];
                })
                ->sortByDesc('total_quantity_sold')
                ->take(10)
                ->values();
        } catch (\Exception $e) {
            Log::error('Sales analytics error', [
                'message' => $e->getMessage(),
                'trace'   => $e->getTraceAsString()
            ]);
            return collect();
        }
    }

    /**
     * Get Top 10 Customers
     * Based on total amount spent
     */
    public function getTopCustomers(): Collection
    {
        try {
            $orders = Order::with('user')
                ->genuine()
                ->get();

            return $orders->groupBy('user_id')
                ->map(function ($group) {
                    $firstOrder = $group->first();
                    return [
                        'customer_id'  => $firstOrder->user_id,
                        'customer_name' => $firstOrder->user ? $firstOrder->user->name : ($firstOrder->full_name ?? 'Guest'),
                        'total_spent'   => $group->sum('total_amount'),
                        'total_orders'  => $group->count(),
                    ];
                })
                ->sortByDesc('total_spent')
                ->take(10)
                ->values();
        } catch (\Exception $e) {
            Log::error('Sales analytics error', [
                'message' => $e->getMessage(),
                'trace'   => $e->getTraceAsString()
            ]);
            return collect();
        }
    }

    /**
     * Get Sales by Category
     * Group by product category
     */
    public function getSalesByCategory(): Collection
    {
        try {
            $items = OrderItem::with('product.category')
                ->whereHas('order', function ($query) {
                    $query->genuine();
                })->get();

            return $items->groupBy(function ($item) {
                return $item->product && $item->product->category ? $item->product->category->name : 'Uncategorized';
            })->map(function ($group, $categoryName) {
                return [
                    'category'         => $categoryName,
                    'total_revenue'    => $group->sum(function ($item) {
                        return $item->quantity * $item->price;
                    }),
                    'total_items_sold' => $group->sum('quantity'),
                ];
            })->sortByDesc('total_revenue')->values();
        } catch (\Exception $e) {
            Log::error('Sales analytics error', [
                'message' => $e->getMessage(),
                'trace'   => $e->getTraceAsString()
            ]);
            return collect();
        }
    }
}
