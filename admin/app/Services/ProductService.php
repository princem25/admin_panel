<?php

namespace App\Services;

use App\Models\Product;
use App\Exceptions\InvalidOrderException;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

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
        
        if (blank($queryParams)) {
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

        // use Custom Collection methods for filtering and sorting
        $collection = $collection->searchTerm($request->input('search'))
            ->byCategory($request->input('category'))
            ->byPriceRange($request->input('min_price'), $request->input('max_price'))
            ->inStock($request->has('in_stock'))
            ->onSale($request->has('on_sale'))
            ->featured($request->has('featured'))
            ->sortProducts($request->input('sort', 'newest'));

        return $this->manuallyPaginate($collection->values(), $page, $request);
    }

    /**
     * Helper to manually paginate a collection.
     */
    protected function manuallyPaginate($collection, $page, $request)
    {
        $perPage = 12;
        // Ensure the collection is indexed cleanly before slicing
        $values = $collection instanceof Collection ? $collection->values() : collect($collection)->values();
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
    /**
     * Track a product as recently viewed in the session.
     */
    public function trackRecentlyViewed(int $productId): void
    {
        $recent = session()->get('recent', []);
        $recent = array_diff($recent, [$productId]); // remove if already exists
        array_unshift($recent, $productId);          // add to front
        $recent = array_slice($recent, 0, 10);         // limit to 10
        session()->put('recent', $recent);
    }

    /**
     * Get recently viewed products, excluding specified IDs.
     */
    public function getRecentlyViewedProducts(array $excludeIds = [], int $limit = 5)
    {
        $recentIds = array_diff(session()->get('recent', []), $excludeIds);
        
        return Product::whereIn('id', $recentIds)
            ->with(['category'])
            ->latest()
            ->take($limit)
            ->get();
    }

    /**
     * Get featured products for home page.
     */
    public function getFeaturedProducts()
    {
        return Cache::tags(['products'])->remember('featured_products', 3600, function () {
            return Product::with('category')
                ->latest()
                ->take(8)
                ->get();
        });
    }

    /**
     * Get products for admin listing.
     */
    public function getProductsForAdmin(array $filters)
    {
        return Product::filter($filters)
            ->latest()
            ->paginate(12)
            ->withQueryString();
    }

    /**
     * Track a product as recently viewed (pushed to end of array).
     */
    public function pushRecentlyViewed(int $productId): void
    {
        $recent = session()->get('recent', []);
        if (! in_array($productId, $recent)) {
            session()->push('recent', $productId);
        }
    }

    /**
     * Create a new product.
     */
    public function createProduct(array $data, $file = null)
    {
        if ($file) {
            $path = $file->store('images', 'public');
            $data['image'] = basename($path);
        }

        if (filled($data['stock'] ?? null) && $data['stock'] === 0) {
            Log::channel('products')->warning('New product created with zero stock', ['name' => $data['name']]);
        }

        $product = tap(Product::create($data), function ($product) {
            Log::info('Model created/updated', ['id' => $product->id]);
        });

        Log::channel('products')->info('Product created successfully', [
            'id'   => $product->id,
            'name' => $product->name,
        ]);

        return $product;
    }

    /**
     * Update an existing product.
     */
    public function updateProduct(Product $product, array $data, $file = null)
    {
        if ($file) {
            if ($product->image && Storage::disk('public')->exists('images/' . $product->image)) {
                Storage::disk('public')->delete('images/' . $product->image);
            }

            $path = $file->store('images', 'public');
            $data['image'] = basename($path);
        }

        // Throw custom exception for records older than 1 year
        if ($product->created_at && $product->created_at->diffInDays(now()) > 365) {
            throw new InvalidOrderException('Products older than 1 year cannot be updated.');
        }

        $product->update($data);

        Log::channel('products')->info('Product updated successfully', ['id' => $product->id]);

        return $product;
    }

    /**
     * Delete a product and its image.
     */
    public function deleteProduct(Product $product): void
    {
        $productId = $product->id;

        if ($product->image && Storage::disk('public')->exists('images/' . $product->image)) {
            Storage::disk('public')->delete('images/' . $product->image);
        }

        $product->delete();

        Log::channel('security')->warning('Product deleted', ['id' => $productId]);
    }
}
