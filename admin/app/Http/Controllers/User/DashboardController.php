<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Services\CustomerAnalyticsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{
    protected $analyticsService;

    public function __construct(CustomerAnalyticsService $analyticsService)
    {
        $this->analyticsService = $analyticsService;
    }

    /**
     * Display the user dashboard with personal order metrics.
     */
    public function index()
    {
        try {
            $user = auth()->user();

            // Use service for detailed analytics
            $stats = $this->analyticsService->getCustomerStats($user->id);
            $topProducts = $this->analyticsService->getTopProducts($user->id);
            $ordersByStatus = $this->analyticsService->getOrdersByStatus($user->id);

            // Keep existing counts for backward compatibility/quick cards if needed
            // but stats already contains total_orders, total_spent, average_order_value
            $totalOrders = $stats['total_orders'];
            $totalSpent = $stats['total_spent'];
            $avgOrderValue = $stats['average_order_value'];

            $activeOrders = $user->orders()->whereNotIn('status', ['delivered', 'cancelled'])->count();
            $deliveredOrders = $user->orders()->where('status', 'delivered')->count();
            $cancelledOrders = $user->orders()->where('status', 'cancelled')->count();

            return view('dashboard', compact(
                'totalOrders',
                'activeOrders',
                'deliveredOrders',
                'cancelledOrders',
                'totalSpent',
                'avgOrderValue',
                'topProducts',
                'ordersByStatus'
            ));
        } catch (\Exception $e) {
            Log::error('User\DashboardController@index error', ['error' => $e->getMessage()]);
            throw $e;
        }
    }
}
