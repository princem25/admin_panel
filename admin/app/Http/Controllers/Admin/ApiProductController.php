<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\ExternalApiService;
use App\Exceptions\ExternalApiException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\Pool;

class ApiProductController extends Controller
{
    protected $apiService;

    public function __construct(ExternalApiService $apiService)
    {
        $this->apiService = $apiService;
    }

    public function index(Request $request)
    {
        $limit = $request->input('limit', 20);
        $category = $request->input('category');

        $categories = [];
        $products = [];
        $error = null;

        try {
            $url = '/products';
            if ($category && $category !== 'all') {
                $url .= '/category/' . urlencode($category);
            }

            $baseUrl = config('services.external_api.base_url', 'https://fakestoreapi.com');
            $token = config('services.external_api.token');

            // --- Measure Sequential Execution Time ---
            $startSeq = microtime(true);
            $this->apiService->client()->get('/products/categories')->throw();
            $this->apiService->client()->get($url, ['limit' => $limit])->throw();
            $timeSeq = microtime(true) - $startSeq;

            // --- Measure Concurrent Execution Time ---
            $startConc = microtime(true);
            $responses = Http::pool(fn (Pool $pool) => [
                $pool->as('categories')->baseUrl($baseUrl)->withToken($token)->timeout(10)->get('/products/categories'),
                $pool->as('products')->baseUrl($baseUrl)->withToken($token)->timeout(15)->get($url, ['limit' => $limit]),
            ]);
            $timeConc = microtime(true) - $startConc;

            Log::info('API Performance Measurement:', [
                'sequential_time_seconds' => round($timeSeq, 4),
                'concurrent_time_seconds' => round($timeConc, 4),
                'difference_seconds' => round($timeSeq - $timeConc, 4),
                'is_faster' => $timeConc < $timeSeq
            ]);

            // Handle categories response gracefully
            if ($responses['categories']->successful()) {
                $categories = $responses['categories']->json();
            } else {
                $categories = []; // fallback
                $error = 'Failed to load categories.';
            }

            // Handle products response gracefully
            if ($responses['products']->successful()) {
                $products = $responses['products']->json();
                Log::info('Successfully fetched API products concurrently.', [
                    'count' => count($products),
                    'category' => $category,
                    'limit' => $limit
                ]);
            } else {
                $products = []; // fallback
                $error = $error ? $error . ' Also failed to load products.' : 'Failed to load products.';
            }

        } catch (RequestException $e) {
            Log::error('API Request Exception.', [
                'status' => $e->response->status(),
                'url' => $e->request->url(),
                'message' => $e->getMessage(),
            ]);
            throw new ExternalApiException('The external service returned an error.');
        } catch (ConnectionException $e) {
            Log::error('API Connection Exception.', [
                'message' => $e->getMessage(),
            ]);
            throw new ExternalApiException('The external service is unreachable.');
        } catch (\Exception $e) {
            Log::error('API Generic Exception.', [
                'message' => $e->getMessage(),
            ]);
            throw new ExternalApiException('An unexpected error occurred.');
        }

        return view('admin.api-products.index', compact('products', 'categories', 'category', 'limit', 'error'));
    }
}
