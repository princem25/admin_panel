<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\AdminService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Display the admin dashboard with order and product summaries.
     */
    public function index(AdminService $adminService)
    {
        try {
            // Cache Duration: 10 Minutes (600 seconds)
            $ttl = 600;

            $totalOrders       = Cache::tags(['admin'])->remember('dash_total_orders', $ttl, fn() => $adminService->countAllOrders());
            $totalRevenue      = Cache::tags(['admin'])->remember('dash_total_revenue', $ttl, fn() => $adminService->sumTotalRevenue());
            $newCustomersToday = Cache::tags(['admin'])->remember('dash_new_customers', $ttl, fn() => $adminService->countNewCustomersToday());
            $pendingOrders     = Cache::tags(['admin'])->remember('dash_pending_orders', $ttl, fn() => $adminService->countPendingOrders());
            $lowStockProducts  = Cache::tags(['admin'])->remember('dash_low_stock', $ttl, fn() => $adminService->countLowStockProducts());

            // These remaining counts aren't explicitly requested to be cached in the 10-min block
            $cancelledOrders = $adminService->countCancelledOrders();
            $genuineOrders = $adminService->countGenuineOrders();
            $revenueByPaymentMethod = $adminService->revenueByPaymentMethod();
            $recentPendingOrders = $adminService->getRecentPendingOrders();

            return view('admin.dashboard', compact(
                'totalOrders',
                'cancelledOrders',
                'genuineOrders',
                'totalRevenue',
                'newCustomersToday',
                'pendingOrders',
                'lowStockProducts',
                'revenueByPaymentMethod',
                'recentPendingOrders'
            ));
        } catch (\Exception $e) {
            Log::error('Admin\DashboardController@index error', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

     public function usersList(Request $request)
    {
        try {
            $query = User::where('role', 'user');

            if ($request->get('sort') === 'oldest') {
                $query->oldest();
            } elseif ($request->get('sort') === 'latest') {
                $query->latest();
            } elseif ($request->get('sort') === 'all') {
                // Leave as default DB order (usually ID asc)
            } else {
                $query->latest(); // Default fallback
            }

            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
                });
            }

            $users = $query->paginate(12)->withQueryString();
            
            return view('admin.users.index', compact('users'));
        } catch (\Exception $e) {
            Log::error('Admin\DashboardController@usersList error', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    public function destroyUser(User $user)
    {
        try {
            // Check if user has orders that are NOT delivered
            $hasActiveOrders = $user->orders()->where('status', '!=', 'delivered')->exists();

            if ($hasActiveOrders) {
                return back()->with('error', 'Cannot delete customer with active or non-delivered orders.');
            }

            $user->delete();

            return back()->with('success', 'Customer deleted successfully.');
        } catch (\Exception $e) {
            Log::error('Admin\DashboardController@destroyUser error', ['error' => $e->getMessage()]);
            return back()->with('error', 'Failed to delete customer.');
        }
    }
}
