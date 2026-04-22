<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class SalesAnalyticsService
{
    /**
     * Get Monthly Sales Report
     * Group orders by month (YYYY-MM)
     */
    public function getMonthlySales(): Collection
    {
        try {
            $orders = Order::genuine()->get();

            return $orders->groupBy(function ($order) {
                return $order->created_at->format('Y-m');
            })->map(function ($group, $month) {
                return [
                    'month'               => $month,
                    'total_revenue'       => $group->sum('total_amount'),
                    'average_order_value' => $group->avg('total_amount'),
                    'total_orders'        => $group->count(),
                ];
            })->sortByDesc('month')->values();
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
