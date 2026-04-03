<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Display the admin dashboard with order and product summaries.
     */
    public function index()
    {
        // Total Orders
        $totalOrders = Order::count();

        // Cancelled Orders
        $cancelledOrders = Order::where('status', 'cancelled')->count();

        // Genuine Orders
        $genuineOrders = Order::where('status', '!=', 'cancelled')->count();

        // Total Revenue from Genuine Orders
        $totalRevenue = Order::where('status', '!=', 'cancelled')->sum('total_amount');

        // Revenue categorized by payment methods (for genuine orders)
        $revenueByPaymentMethod = Order::where('status', '!=', 'cancelled')
            ->select('payment_method', DB::raw('SUM(total_amount) as total'))
            ->groupBy('payment_method')
            ->get();

        return view('admin.dashboard', compact(
            'totalOrders',
            'cancelledOrders',
            'genuineOrders',
            'totalRevenue',
            'revenueByPaymentMethod'
        ));
    }
}
