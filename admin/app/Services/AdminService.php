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
        return  Order::where('status', '!=', 'cancelled')->count();
    }

    public function sumTotalRevenue()
    {
        return  Order::where('status', '!=', 'cancelled')->sum('total_amount');
    }

    public function revenueByPaymentMethod()
    {
        return  Order::where('status', '!=', 'cancelled')
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

    public function countLowStockProducts()
    {
        return Product::where('stock', '<=', 5)->count();
    }
}
