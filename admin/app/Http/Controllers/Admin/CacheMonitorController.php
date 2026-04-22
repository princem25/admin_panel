<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;

class CacheMonitorController extends Controller
{
    /**
     * Display the cache performance monitor page.
     * Read-only — does not modify any existing cache logic.
     */
    public function index()
    {
        // --- Redis Stats (safe read-only calls) ---
        try {
            $raw  = Redis::info();

            // Flatten nested sections (Predis returns sections as nested arrays;
            // phpredis returns a flat array). This handles both drivers.
            $info = [];
            foreach ($raw as $key => $value) {
                if (is_array($value)) {
                    $info = array_merge($info, $value);
                } else {
                    $info[$key] = $value;
                }
            }

            $totalKeys  = Redis::dbSize();
            $usedMemory = isset($info['used_memory'])
                            ? round($info['used_memory'] / 1048576, 2) . ' MB'
                            : 'N/A';
            $peakMemory = isset($info['used_memory_peak'])
                            ? round($info['used_memory_peak'] / 1048576, 2) . ' MB'
                            : 'N/A';
            $uptimedays = $info['uptime_in_days']         ?? 'N/A';
            $hitCount   = $info['keyspace_hits']          ?? null;
            $missCount  = $info['keyspace_misses']        ?? null;

            $hitRate = ($hitCount !== null && $missCount !== null && ($hitCount + $missCount) > 0)
                        ? round(($hitCount / ($hitCount + $missCount)) * 100, 1)
                        : null;

            $redisAvailable = true;
        } catch (\Exception $e) {
            $totalKeys      = 'N/A';
            $usedMemory     = 'N/A';
            $peakMemory     = 'N/A';
            $uptimedays     = 'N/A';
            $hitRate        = null;
            $hitCount       = null;
            $missCount      = null;
            $redisAvailable = false;
        }

        // --- Known Application Cache Keys (static registry — non-intrusive) ---
        $knownCacheGroups = [
            'products' => [
                'tag'   => 'products',
                'color' => 'blue',
                'icon'  => '📦',
                'ttl'   => '30–60 min',
                'keys'  => [
                    'featured_products',
                    'products_default_view_page_{$page}',
                    'product_{id}  (user detail view)',
                    'product_{id}  (cart service)',
                    'categories',
                    'category_summary',
                ],
            ],
            'admin' => [
                'tag'   => 'admin',
                'color' => 'purple',
                'icon'  => '🛠️',
                'ttl'   => '10 min',
                'keys'  => [
                    'dash_total_orders',
                    'dash_total_revenue',
                    'dash_new_customers',
                    'dash_pending_orders',
                    'dash_low_stock',
                    'categories  (shared)',
                    'category_summary  (shared)',
                ],
            ],
            'customer' => [
                'tag'   => 'customer',
                'color' => 'green',
                'icon'  => '🛒',
                'ttl'   => '10 min',
                'keys'  => [
                    'cart_summary_{user_id}',
                ],
            ],
        ];

        // --- Quick-check: are known singleton keys currently cached? ---
        $singletonChecks = [
            'featured_products'  => Cache::tags(['products'])->has('featured_products'),
            'categories'         => Cache::tags(['products', 'admin'])->has('categories'),
            'category_summary'   => Cache::tags(['products', 'admin'])->has('category_summary'),
            'dash_total_orders'  => Cache::tags(['admin'])->has('dash_total_orders'),
            'dash_total_revenue' => Cache::tags(['admin'])->has('dash_total_revenue'),
        ];

        return view('admin.cache.index', compact(
            'totalKeys',
            'usedMemory',
            'peakMemory',
            'uptimedays',
            'hitRate',
            'hitCount',
            'missCount',
            'redisAvailable',
            'knownCacheGroups',
            'singletonChecks'
        ));
    }

    /**
     * Flush the entire application cache.
     * This is the ONLY write operation on this page.
     */
    public function flush(Request $request)
    {
        Cache::flush();

        return redirect()->route('admin.cache.index')
            ->with('success', 'All application cache has been cleared successfully.');
    }
}
