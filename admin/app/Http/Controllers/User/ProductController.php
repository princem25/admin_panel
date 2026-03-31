<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        Log::debug('User browsing products', $request->only(['search', 'category', 'price']));

        $products = Product::filter($request->only(['search', 'category', 'price']))
            ->with('category')
            ->latest()
            ->get();

        $cart = session()->get('cart', []);
        $cartProductIds = collect($cart)->pluck('product_id')->toArray();

        // User Preference: apply session theme
        $theme = session('theme', 'light');

        Log::channel('products')->info('User viewed product listing', ['count' => $products->count()]);

        return view('user.products', compact('products', 'cartProductIds', 'theme'));
    }

    public function show(Product $product)
    {
        try {
            $product->load('category');

            // Session: track recently viewed products
            $recent = session()->get('recent', []);
            if (! in_array($product->id, $recent)) {
                session()->push('recent', $product->id);
            }

            // Log::info — user viewed a single product page
            Log::channel('products')->info('User viewed product', [
                'product_id'   => $product->id,
                'product_name' => $product->name,
            ]);

            $cart = session()->get('cart', []);
            $cartProductIds = collect($cart)->pluck('product_id')->toArray();

            // Fetch recently viewed products excluding the current one
            $recentIds = session()->get('recent', []);
            $recentProducts = Product::whereIn('id', $recentIds)
                ->where('id', '!=', $product->id)
                ->latest()
                ->take(4)
                ->get();

            return view('user.show', compact('product', 'cartProductIds', 'recentProducts'));
        } catch (ModelNotFoundException $e) {
            Log::error('Product not found', ['error' => $e->getMessage()]);
            abort(404);
        } catch (\Exception $e) {
            Log::error('Error showing product', ['error' => $e->getMessage()]);
            return back()->with('error', 'Could not load the product.');
        }
    }
}

