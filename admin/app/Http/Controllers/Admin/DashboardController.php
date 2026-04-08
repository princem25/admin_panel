<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\AdminService;
use Illuminate\Support\Facades\Cache;

class DashboardController extends Controller
{
    /**
     * Display the admin dashboard with order and product summaries.
     */
    public function index(AdminService $adminService)
    {
        // Cache Duration: 10 Minutes (600 seconds)
        $ttl = 600;

        $totalOrders = Cache::remember('dash_total_orders', $ttl, fn() => $adminService->countAllOrders());
        $totalRevenue = Cache::remember('dash_total_revenue', $ttl, fn() => $adminService->sumTotalRevenue());
        $newCustomersToday = Cache::remember('dash_new_customers', $ttl, fn() => $adminService->countNewCustomersToday());
        $pendingOrders = Cache::remember('dash_pending_orders', $ttl, fn() => $adminService->countPendingOrders());
        $lowStockProducts = Cache::remember('dash_low_stock', $ttl, fn() => $adminService->countLowStockProducts());

        // These remaining counts aren't explicitly requested to be cached in the 10-min block
        $cancelledOrders = $adminService->countCancelledOrders();
        $genuineOrders = $adminService->countGenuineOrders();
        $revenueByPaymentMethod = $adminService->revenueByPaymentMethod();

        return view('admin.dashboard', compact(
            'totalOrders',
            'cancelledOrders',
            'genuineOrders',
            'totalRevenue',
            'newCustomersToday',
            'pendingOrders',
            'lowStockProducts',
            'revenueByPaymentMethod'
        ));
    }

     public function usersList()
    {
        // Fetch users who are not admins (customers)
        $users = User::where('role', 'user')->latest()->paginate(12);
        
        return view('admin.users.index', compact('users'));
    }
}
