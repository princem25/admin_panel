<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\OrderAnalytics;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DatabaseTestController extends Controller
{
    /**
     * Test multiple database connections.
     */
    public function testConnections()
    {
        // 1. Query the main database using the default User model
        $users = User::limit(5)->get();

        // 2. Query the analytics database using the OrderAnalytics model
        $analytics = OrderAnalytics::limit(5)->get();

        // 3. Use a raw query with DB::connection('analytics')->select(...)
        $rawAnalytics = DB::connection('analytics')->select('SELECT * FROM order_analytics LIMIT 5');

        // Return all results as JSON response
        return response()->json([
            'success' => true,
            'main_db_users' => $users,
            'analytics_db_eloquent' => $analytics,
            'analytics_db_raw' => $rawAnalytics,
        ]);
    }
}
