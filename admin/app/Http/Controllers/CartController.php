<?php

namespace App\Http\Controllers;

use App\Exceptions\ProductOutOfStockException;
use App\Models\Product;
use App\Services\CartService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Events\Customer\ProductAddedToCart;
use App\Events\Customer\CartAbandoned;

class CartController extends Controller
{
    protected CartService $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    // View Cart
    public function index()
    {
        $summary      = $this->cartService->getCartSummary();
        $cartItems    = $summary['items'];
        $grandTotal   = $summary['grandTotal'];
        $totalSavings = $summary['totalSavings'];
        $sessiondata  = session()->get('cart');

        // Recently Viewed Items
        $inCartIds = collect($cartItems)->pluck('product_id')->toArray();
        $recentIds = array_diff(session()->get('recent', []), $inCartIds);
        
        $recentProducts = Product::whereIn('id', $recentIds)
            ->with(['category'])
            ->latest()
            ->take(5)
            ->get();

        return view('cart.index', compact(
            'cartItems', 
            'grandTotal', 
            'totalSavings', 
            'sessiondata',
            'recentProducts'
        ));
    }

    // Add to Cart
    public function add(Product $product)
    {
        try {
            // CartService handles all stock validation and DB decrement
            $this->cartService->addToCart($product->id, 1);

            // Also track as 'recently viewed' when added to cart
            $recent = session()->get('recent', []);
            $recent = array_diff($recent, [$product->id]); // remove if already exists
            array_unshift($recent, $product->id);          // add to front
            $recent = array_slice($recent, 0, 10);         // limit to 10
            $recent = array_slice($recent, 0, 10);         // limit to 10
            session()->put('recent', $recent);

            // Fire event for product added to cart
            event(new ProductAddedToCart($product, auth()->user()));

            return back()->with('success', "'{$product->name}' added to cart!");
        } catch (ProductOutOfStockException $e) {
            Log::warning('Out of stock add attempt', [
                'product_id' => $product->id,
                'message'    => $e->getMessage(),
            ]);

            return back()->with('error', $e->getMessage());
        } catch (\Exception $e) {
            Log::error('Cart Add Error', ['error' => $e->getMessage()]);
            return back()->with('error', $e->getMessage());
        }
    }

    // Remove from Cart — stock is auto-restored in CartService
    public function remove(Product $product, Request $request)
    {
        try {
            $this->cartService->remove($product->id);
            $message = 'Product removed from cart!';
            session()->flash('success', $message);

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $message,
                    'summary' => $this->cartService->getCartSummary(),
                    'flash_html' => view('components.flash-message')->render(),
                ]);
            }

            return back()->with('success', $message);
        } catch (\Exception $e) {
            $message = $e->getMessage();
            Log::error('Cart Remove Error', ['error' => $message]);
            session()->flash('error', $message);

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $message,
                    'flash_html' => view('components.flash-message')->render(),
                ], 500);
            }

            return back()->with('error', $message);
        }
    }

    // Clear Cart — all stock auto-restored
    public function clear(Request $request)
    {
        try {
            $this->cartService->clearCart();
            $message = 'Cart cleared!';
            session()->flash('success', $message);

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $message,
                    'summary' => $this->cartService->getCartSummary(),
                    'flash_html' => view('components.flash-message')->render(),
                ]);
            }

            return back()->with('success', $message);
        } catch (\Exception $e) {
            $message = $e->getMessage();
            Log::error('Cart Clear Error', ['error' => $message]);
            session()->flash('error', $message);

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $message,
                    'flash_html' => view('components.flash-message')->render(),
                ], 500);
            }

            return back()->with('error', $message);
        }
    }

    // Increase Quantity — checks stock via CartService
    public function increase($id, Request $request)
    {
        try {
            $this->cartService->increase((int) $id);
            $message = 'Quantity increased!';
            session()->flash('success', $message);

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $message,
                    'summary' => $this->cartService->getCartSummary(),
                    'flash_html' => view('components.flash-message')->render(),
                ]);
            }
        } catch (\Exception $e) {
            $message = $e->getMessage();
            Log::error('Cart Increase Error', ['error' => $message]);
            session()->flash('error', $message);
            
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $message,
                    'flash_html' => view('components.flash-message')->render(),
                ], 400);
            }

            return back()->with('error', $message);
        }

        return back()->with('success', 'Quantity increased!');
    }

    // Decrease Quantity — restores 1 unit of stock
    public function decrease($id, Request $request)
    {
        try {
            $this->cartService->decrease((int) $id);
            $message = 'Quantity decreased!';
            session()->flash('success', $message);

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $message,
                    'summary' => $this->cartService->getCartSummary(),
                    'flash_html' => view('components.flash-message')->render(),
                ]);
            }
        } catch (\Exception $e) {
            $message = $e->getMessage();
            Log::error('Cart Decrease Error', ['error' => $message]);
            session()->flash('error', $message);
            
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $message,
                    'flash_html' => view('components.flash-message')->render(),
                ], 400);
            }

            return back()->with('error', $message);
        }

        return back();
    }

    // Simulate Abandoned Cart
    public function simulateAbandon(Request $request)
    {
        $cart = session()->get('cart', []);
        
        event(new CartAbandoned($cart));
        
        return response()->json(['message' => 'Cart Abandoned event fired!']);
    }
}
