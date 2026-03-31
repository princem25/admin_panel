<?php

namespace App\Http\Controllers;

use App\Exceptions\ProductOutOfStockException;
use App\Models\Product;
use App\Services\CartService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

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

        return view('cart.index', compact('cartItems', 'grandTotal', 'totalSavings', 'sessiondata'));
    }

    // Add to Cart
    public function add(Product $product)
    {
        try {
            // CartService handles all stock validation and DB decrement
            $this->cartService->addToCart($product->id, 1);

            Log::info('Product added to cart', ['product_id' => $product->id]);

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
    public function remove(Product $product)
    {
        try {
            $this->cartService->remove($product->id);

            return back()->with('success', 'Product removed from cart!');
        } catch (\Exception $e) {
            Log::error('Cart Remove Error', ['error' => $e->getMessage()]);
            return back()->with('error', $e->getMessage());
        }
    }

    // Clear Cart — all stock auto-restored
    public function clear()
    {
        try {
            $this->cartService->clearCart();

            return back()->with('success', 'Cart cleared!');
        } catch (\Exception $e) {
            Log::error('Cart Clear Error', ['error' => $e->getMessage()]);
            return back()->with('error', $e->getMessage());
        }
    }

    // Increase Quantity — checks stock via CartService
    public function increase($id)
    {
        try {
            $this->cartService->increase((int) $id);
        } catch (\Exception $e) {
            Log::error('Cart Increase Error', ['error' => $e->getMessage()]);
            return back()->with('error', $e->getMessage());
        }

        return back()->with('success', 'Quantity increased!');
    }

    // Decrease Quantity — restores 1 unit of stock
    public function decrease($id)
    {
        try {
            $this->cartService->decrease((int) $id);
        } catch (\Exception $e) {
            Log::error('Cart Decrease Error', ['error' => $e->getMessage()]);
            return back()->with('error', $e->getMessage());
        }

        return back();
    }
}
