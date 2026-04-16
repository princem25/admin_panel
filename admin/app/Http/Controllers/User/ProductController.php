<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Services\ProductService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Cache;
use App\Events\Customer\ProductViewed;
use App\Models\ProductWaitlist;

class ProductController extends Controller
{
    protected $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }

    public function index(Request $request)
    {
        Log::debug('User browsing products', $request->all());

        // Delegate all complex filtering, sorting, and caching to the Service
        $products = $this->productService->getFilteredProducts($request);

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
            $recent = session()->get('recent', []);
            $recent = array_diff($recent, [$product->id]); // remove if already exists
            array_unshift($recent, $product->id);          // add to front
            $recent = array_slice($recent, 0, 10);         // limit to 10
            session()->put('recent', $recent);

            // Fire event for product viewed
            event(new ProductViewed($product, auth()->user()));

            $cart = session()->get('cart', []);
            $cartProductIds = collect($cart)->pluck('product_id')->toArray();

            // Fetch recently viewed products excluding the current one
            $recentIds = session()->get('recent', []);
            $recentProducts = Product::whereIn('id', $recentIds)
                ->where('id', '!=', $product->id)
                ->latest()
                ->take(4)
                ->get();

            // Check if the user is already on waitlist (for showing button state)
            $onWaitlist = auth()->check()
                ? ProductWaitlist::where('product_id', $product->id)
                    ->where('user_id', auth()->id())
                    ->exists()
                : false;

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

