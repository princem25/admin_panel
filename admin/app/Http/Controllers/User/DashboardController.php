<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Display the user dashboard with personal order metrics.
     */
    public function index()
    {
        $user = auth()->user();

        // Total Orders
        $totalOrders = $user->orders()->count();

        // Active Orders (Not Delivered, Not Cancelled)
        $activeOrders = $user->orders()->whereNotIn('status', ['delivered', 'cancelled'])->count();

        // Delivered Orders
        $deliveredOrders = $user->orders()->where('status', 'delivered')->count();

        // Cancelled Orders
        $cancelledOrders = $user->orders()->where('status', 'cancelled')->count();

        // Total Spent (Only on genuine / non-cancelled orders)
        $totalSpent = $user->orders()->where('status', '!=', 'cancelled')->sum('total_amount');

        return view('dashboard', compact(
            'totalOrders',
            'activeOrders',
            'deliveredOrders',
            'cancelledOrders',
            'totalSpent'
        ));
    }
}
