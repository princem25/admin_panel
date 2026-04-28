<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\ExternalApiService;
use Illuminate\Support\Facades\Log;

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
            // Fetch categories for the filter dropdown
            $categoryResponse = $this->apiService->client()->get('/products/categories');
            if ($categoryResponse->successful()) {
                $categories = $categoryResponse->json();
            }

            // Fetch products based on selected category
            $url = '/products';
            if ($category && $category !== 'all') {
                $url .= '/category/' . urlencode($category);
            }

            $productResponse = $this->apiService->client()->get($url, [
                'limit' => $limit
            ]);

            if ($productResponse->successful()) {
                $products = $productResponse->json();
                Log::info('Successfully fetched API products.', [
                    'count' => count($products),
                    'category' => $category,
                    'limit' => $limit
                ]);
            } else {
                $error = 'Failed to fetch products from the API. (Status: ' . $productResponse->status() . ')';
                Log::error('API Product Fetch Failed.', [
                    'status' => $productResponse->status(),
                    'url' => $url,
                    'category' => $category
                ]);
            }
        } catch (\Exception $e) {
            $error = 'An error occurred while connecting to the API: ' . $e->getMessage();
            Log::error('API Product Fetch Exception.', [
                'message' => $e->getMessage(),
                'url' => $url ?? '/products'
            ]);
        }

        return view('admin.api-products.index', compact('products', 'categories', 'category', 'limit', 'error'));
    }
}
