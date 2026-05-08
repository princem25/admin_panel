<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Services\ProductService;
use App\Services\WaitlistService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Cache;
use App\Events\Customer\ProductViewed;
use Illuminate\Support\Arr;

class ProductController extends Controller
{
    protected ProductService $productService;
    protected WaitlistService $waitlistService;

    public function __construct(ProductService $productService, WaitlistService $waitlistService)
    {
        $this->productService = $productService;
        $this->waitlistService = $waitlistService;
    }

    public function index(Request $request)
    {
        Log::debug('User browsing products', Arr::only($request->all(), ['search', 'category', 'price', 'sort']));

        // Delegate all complex filtering, sorting, and caching to the Service
        $products = $this->productService->getFilteredProducts($request);

        if (Arr::has($request->all(), 'category')) {
            Log::debug('Category filter applied by user');
        }

        $cart = session()->get('cart', []);
        $cartProductIds = collect($cart)->pluck('product_id')->toArray();

        // Fetch categories for the filter dropdown
        $categories = Category::all();

        // User Preference: apply session theme
        $theme = session('theme', 'light');

        Log::channel('products')->info('User viewed product listing (Service Layer Processed)', [
            'total_found' => $products->total(),
            'current_page' => $products->currentPage()
        ]);

        return view('user.products', compact('products', 'cartProductIds', 'theme', 'categories'));
    }

    public function show(Product $product)
    {
        try {
            $cacheKey = "product_{$product->id}";

            $product = Cache::tags(['products'])->remember($cacheKey, 1800, function () use ($product) {
                return $product->load('category');
            });

            // Session: track recently viewed products (unique, capped at 10, newest first)
            $this->productService->trackRecentlyViewed($product->id);

            // Fire event for product viewed
            event(new ProductViewed($product, auth()->user()));

            $cart = session()->get('cart', []);
            $cartProductIds = collect($cart)->pluck('product_id')->toArray();

            // Fetch recently viewed products excluding the current one
            $recentProducts = $this->productService->getRecentlyViewedProducts([$product->id], 4);

            // Check if the user is already on waitlist (for showing button state)
            $onWaitlist = $this->waitlistService->isUserOnWaitlist(auth()->user(), $product->id);

            return view('user.show', compact('product', 'cartProductIds', 'recentProducts', 'onWaitlist'));
        } catch (ModelNotFoundException $e) {
            Log::error('Product not found', ['error' => $e->getMessage()]);
            abort(404);
        } catch (\Exception $e) {
            Log::error('Error showing product', ['error' => $e->getMessage()]);
            return back()->with('error', 'Could not load the product.');
        }
    }
}

