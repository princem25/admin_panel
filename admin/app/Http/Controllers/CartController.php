<?php

namespace App\Http\Controllers;

use App\Exceptions\ProductOutOfStockException;
use App\Models\Product;
use App\Services\CartService;
use App\Services\ProductService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Events\Customer\CartAbandoned;
use Illuminate\Support\Arr;

class CartController extends Controller
{
    protected CartService $cartService;
    protected ProductService $productService;

    public function __construct(CartService $cartService, ProductService $productService)
    {
        $this->cartService = $cartService;
        $this->productService = $productService;
    }

    // View Cart
    public function index()
    {
        try {
            $summary      = $this->cartService->getCartSummary();
            $cartItems    = Arr::get($summary, 'items', []);
            $grandTotal   = Arr::get($summary, 'grandTotal', 0);
            $totalSavings = Arr::get($summary, 'totalSavings', 0);
            $sessiondata  = session()->get('cart');

            // Recently Viewed Items
            $inCartIds = Arr::pluck($cartItems, 'product_id');
            $recentProducts = $this->productService->getRecentlyViewedProducts($inCartIds);

            return view('cart.index', compact(
                'cartItems', 
                'grandTotal', 
                'totalSavings', 
                'sessiondata',
                'recentProducts'
            ));
        } catch (\Exception $e) {
            Log::error('CartController@index error', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    // Add to Cart
    public function add(Product $product, Request $request)
    {
        try {
            // CartService handles all stock validation, DB decrement, tracking, and events
            $this->cartService->addToCart($product->id, 1);

            return $this->cartResponse($request, "'{$product->name}' added to cart!");
        } catch (ProductOutOfStockException $e) {
            Log::warning('Out of stock add attempt', [
                'product_id' => $product->id,
                'message'    => $e->getMessage(),
            ]);

            return $this->cartResponse($request, $e->getMessage(), false, 400);
        } catch (\Exception $e) {
            Log::error('Cart Add Error', ['error' => $e->getMessage()]);
            return $this->cartResponse($request, $e->getMessage(), false, 500);
        }
    }

    // Remove from Cart
    public function remove(Product $product, Request $request)
    {
        try {
            $this->cartService->remove($product->id);
            return $this->cartResponse($request, 'Product removed from cart!');
        } catch (\Exception $e) {
            Log::error('Cart Remove Error', ['error' => $e->getMessage()]);
            return $this->cartResponse($request, $e->getMessage(), false, 500);
        }
    }

    // Clear Cart
    public function clear(Request $request)
    {
        try {
            $this->cartService->clearCart();
            return $this->cartResponse($request, 'Cart cleared!');
        } catch (\Exception $e) {
            Log::error('Cart Clear Error', ['error' => $e->getMessage()]);
            return $this->cartResponse($request, $e->getMessage(), false, 500);
        }
    }

    // Increase Quantity
    public function increase($id, Request $request)
    {
        try {
            $this->cartService->increase((int) $id);
            return $this->cartResponse($request, 'Quantity increased!');
        } catch (\Exception $e) {
            Log::error('Cart Increase Error', ['error' => $e->getMessage()]);
            return $this->cartResponse($request, $e->getMessage(), false, 400);
        }
    }

    // Decrease Quantity
    public function decrease($id, Request $request)
    {
        try {
            $this->cartService->decrease((int) $id);
            return $this->cartResponse($request, 'Quantity decreased!');
        } catch (\Exception $e) {
            Log::error('Cart Decrease Error', ['error' => $e->getMessage()]);
            return $this->cartResponse($request, $e->getMessage(), false, 400);
        }
    }

    // Simulate Abandoned Cart
    public function simulateAbandon(Request $request)
    {
        try {
            $cart = session()->get('cart', []);
            
            event(new CartAbandoned($cart));
            
            return response()->json(['message' => 'Cart Abandoned event fired!']);
        } catch (\Exception $e) {
            Log::error('CartController@simulateAbandon error', ['error' => $e->getMessage()]);
            return response()->json(['message' => 'Failed to fire event.'], 500);
        }
    }

    /**
     * Helper to handle responses consistently (AJAX vs Normal).
     */
    private function cartResponse(Request $request, string $message, bool $success = true, int $statusCode = 200)
    {
        session()->flash($success ? 'success' : 'error', $message);

        if ($request->ajax() || $request->wantsJson()) {
            $data = [
                'success' => $success,
                'message' => $message,
                'flash_html' => view('components.flash-message')->render(),
            ];

            if ($success) {
                $data['summary'] = $this->cartService->getCartSummary();
            }

            return response()->json($data, $statusCode);
        }

        return back()->with($success ? 'success' : 'error', $message);
    }
}
