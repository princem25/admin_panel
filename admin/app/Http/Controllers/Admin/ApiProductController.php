<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ApiProductController extends Controller
{
    public function index(Request $request)
    {
        $limit = $request->input('limit', 20);
        $category = $request->input('category');

        $categories = [];
        $products = [];
        $error = null;

        try {
            // Fetch categories for the filter dropdown
            $categoryResponse = Http::timeout(10)->get('https://fakestoreapi.com/products/categories');
            if ($categoryResponse->successful()) {
                $categories = $categoryResponse->json();
            }

            // Fetch products based on selected category
            $url = 'https://fakestoreapi.com/products';
            if ($category && $category !== 'all') {
                $url .= '/category/' . urlencode($category);
            }

            $productResponse = Http::timeout(15)->get($url, [
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
                'url' => $url ?? 'https://fakestoreapi.com/products'
            ]);
        }

        return view('admin.api-products.index', compact('products', 'categories', 'category', 'limit', 'error'));
    }
}
