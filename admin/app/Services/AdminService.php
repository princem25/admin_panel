<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class AdminService
{
    public function countAllOrders()
    {
        return  Order::count();
    }
    public function countCancelledOrders()
    {
        return  Order::where('status', 'cancelled')->count();
    }

    public function countGenuineOrders()
    {
        return  Order::genuine()->count();
    }

    public function sumTotalRevenue()
    {
        return  Order::genuine()->sum('total_amount');
    }

    public function revenueByPaymentMethod()
    {
        return  Order::genuine()
            ->select('payment_method', DB::raw('SUM(total_amount) as total'))
            ->groupBy('payment_method')
            ->get();
    }

    public function stockCheck()
    {
        return Product::with('category')
            ->select('id', 'name', 'category_id', 'stock')
            ->get();
    }

    public function countNewCustomersToday()
    {
        return User::where('role', 'user')->whereDate('created_at', now())->count();
    }
    
    public function countPendingOrders()
    {
        return Order::where('status', 'pending')->count();
    }

    public function getRecentPendingOrders($limit = 5)
    {
        return Order::with('user')
            ->where('status', 'pending')
            ->latest()
            ->take($limit)
            ->get();
    }

    public function countLowStockProducts()
    {
        return Product::where('stock', '<=', 5)->count();
    }
}
