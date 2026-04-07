<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\User;
use App\Services\AdminService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Display the admin dashboard with order and product summaries.
     */
    public function index(AdminService $adminService)
    {
        // Total Orders
        $totalOrders = $adminService->countAllOrders();

        // Cancelled Orders
        $cancelledOrders = $adminService->countCancelledOrders();

        // Genuine Orders
        $genuineOrders = $adminService->countGenuineOrders();

        // Total Revenue from Genuine Orders
        $totalRevenue = $adminService->sumTotalRevenue();

        // Revenue categorized by payment methods (for genuine orders)
        $revenueByPaymentMethod = $adminService->revenueByPaymentMethod();

        return view('admin.dashboard', compact(
            'totalOrders',
            'cancelledOrders',
            'genuineOrders',
            'totalRevenue',
            'revenueByPaymentMethod'
        ));
    }

     public function usersList()
    {
        // Fetch users who are not admins (customers)
        $users = User::where('role', 'user')->latest()->paginate(10);
        
        return view('admin.users.index', compact('users'));
    }
}
