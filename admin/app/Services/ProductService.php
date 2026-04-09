<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Cache;

class ProductService
{
    /**
     * Get filtered and sorted products using Collection methods and Cache Tags.
     *
     * @param Request $request
     * @return LengthAwarePaginator
     */
    public function getFilteredProducts(Request $request)
    {
        $page = $request->input('page', 1);
        $queryParams = $request->except('page');
        
        if (empty($queryParams)) {
            $cacheKey = "products_default_view_page_{$page}";
            
            // Check cache first
            if ($cached = Cache::tags(['products'])->get($cacheKey)) {
                return $cached;
            }

            // Fetch from DB
            $allProducts = Product::with('category')->latest()->get()->values();
            $paginator = $this->manuallyPaginate($allProducts, $page, $request);

            // ⚡ SMART CACHE: Only cache if we actually have products
            if ($allProducts->isNotEmpty()) {
                Cache::tags(['products'])->put($cacheKey, $paginator, 3600);
            }

            return $paginator;
        }

        // --- 🔍 DYNAMIC LANE: If filters exist, use the "Cache Base + Filter In-Memory" strategy ---
        $baseKey = 'all_products_base';
        $allProducts = Cache::tags(['products'])->get($baseKey);

        if (!$allProducts) {
            $allProducts = Product::with('category')->get();
            // ⚡ SMART CACHE: Only cache the base collection if it's not empty
            if ($allProducts->isNotEmpty()) {
                Cache::tags(['products'])->put($baseKey, $allProducts, 3600);
            }
        }

        $collection = $allProducts;

        // Search Filter
        if ($term = $request->input('search')) {
            $collection = $collection->filter(function ($product) use ($term) {
                return str_contains(strtolower($product->name), strtolower($term));
            });
        }

        // Category Filter
        if ($catId = $request->input('category')) {
            $categories = is_array($catId) ? $catId : [$catId];
            $collection = $collection->whereIn('category_id', $categories);
        }

        // Price Range Filter
        $min = $request->input('min_price');
        $max = $request->input('max_price');
        if ($min !== null || $max !== null) {
            $collection = $collection->filter(function ($product) use ($min, $max) {
                $price = $product->discount_price ?? $product->price;
                $minPass = $min === null || $price >= $min;
                $maxPass = $max === null || $price <= $max;
                return $minPass && $maxPass;
            });
        }

        // Quick Filters
        if ($request->has('in_stock')) {
            $collection = $collection->where('stock', '>', 0);
        }
        if ($request->has('on_sale')) {
            $collection = $collection->filter(function ($product) {
                $discount = $product->price - ($product->discount_price ?? $product->price);
                return $discount > 0;
            });
        }

        // Sorting
        $collection = match ($request->input('sort', 'newest')) {
            'price_low'  => $collection->sortBy('price'),
            'price_high' => $collection->sortByDesc('price'),
            'popularity' => $collection->sortByDesc('stock'),
            default      => $collection->sortByDesc('created_at'),
        };

        return $this->manuallyPaginate($collection->values(), $page, $request);
    }

    /**
     * Helper to manually paginate a collection.
     */
    protected function manuallyPaginate($collection, $page, $request)
    {
        $perPage = 12;
        // Ensure the collection is indexed cleanly before slicing
        $values = $collection instanceof \Illuminate\Support\Collection ? $collection->values() : collect($collection)->values();
        $items = $values->forPage($page, $perPage);
        
        return new LengthAwarePaginator(
            $items,
            $collection->count(),
            $perPage,
            $page,
            [
                'path' => Paginator::resolveCurrentPath(),
                'query' => $request->query(),
            ]
        );
    }

    public function all()
    {
        return Product::with('category')->latest()->get();
    }

    public function paginate(int $perPage = 10)
    {
        return Product::with('category')->latest()->paginate($perPage);
    }

    public function exportCsv()
    {
        $products = Product::with('category')->get();

        return function () use ($products) {
            $file = fopen('php://output', 'w');

            // Header
            fputcsv($file, ['Name', 'Price', 'Discount Price', 'Description', 'Stock']);

            foreach ($products as $product) {
                fputcsv($file, [
                    $product->name,
                    $product->price,
                    $product->discount_price ? $product->discount_price : $product->price,
                    $product->description,
                    $product->stock,
                 ]);
            }

            fclose($file);
        };
    }

    public function getCsvString()
    {
        $products = Product::with('category')->get();

        $handle = fopen('php://temp', 'r+');

        fputcsv($handle, ['Name', 'Price', 'Discount Price', 'Description', 'Stock']);

        foreach ($products as $product) {
            fputcsv($handle, [
                $product->name,
                $product->price,
                $product->discount_price ?? $product->price,
                $product->description,
                $product->stock,
            ]);
        }

        rewind($handle);
        $csv = stream_get_contents($handle);
        fclose($handle);

        return $csv;
    }
}
