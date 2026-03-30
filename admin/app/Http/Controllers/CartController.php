<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Services\CartService;
use Illuminate\Http\Request;

class CartController extends Controller
{
    protected $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    // View Cart
    public function index()
    {
        $summary = $this->cartService->getCartSummary();
        $cartItems = $summary['items'];
        $grandTotal = $summary['grandTotal'];
        $sessiondata = session()->get('cart');

        return view('cart.index', compact('cartItems', 'grandTotal', 'sessiondata'));
    }

    // Add to Cart
    public function add(Product $product)
    {
        try {
            $this->cartService->addToCart($product->id, 1);
            return back()->with('success', 'Product added to cart!');
        } catch (\Exception $e) {
            return back()->with('error', 'Could not add product to cart.');
        }
    }

    // Remove from Cart
    public function remove(Product $product)
    {
        try {
            $this->cartService->remove($product->id);
            return back()->with('success', 'Product removed from cart!');
        } catch (\Exception $e) {
            return back()->with('error', 'Could not remove product from cart.');
        }
    }

    // Clear Cart
    public function clear()
    {
        try {
            $this->cartService->clearCart();
            return back()->with('success', 'Cart cleared!');
        } catch (\Exception $e) {
            return back()->with('error', 'Could not clear cart.');
        }
    }

    // Increase Quantity
    public function increase($id)
    {
        $this->cartService->increase($id);
        return back();
    }

    // Decrease Quantity
    public function decrease($id)
    {
        $this->cartService->decrease($id);
        return back();
    }
}
