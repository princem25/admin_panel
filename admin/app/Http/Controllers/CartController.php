<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cart;
use App\Models\Product;

class CartController extends Controller
{
    //  View Cart
    public function index()
    {
        $cartItems = Cart::with('product')
            ->where('user_id', auth()->id())
            ->get();

        return view('cart.index', compact('cartItems'));
    }

    //  Add to Cart
    public function add(Product $product)
    {
        $cart = Cart::where('user_id', auth()->id())
            ->where('product_id', $product->id)
            ->first();

        if ($cart) {
            $cart->increment('quantity');
        } else {
            Cart::create([
                'user_id' => auth()->id(),
                'product_id' => $product->id,
                'quantity' => 1
            ]);
        }

        return back()->with('success', 'Product added to cart!');
    }

    //  Remove from Cart
    public function remove(Product $product)
    {
        Cart::where('user_id', auth()->id())
            ->where('product_id', $product->id)
            ->delete();

        return back()->with('success', 'Product removed from cart!');
    }

    //  Clear Cart
    public function clear()
    {
        Cart::where('user_id', auth()->id())->delete();

        return back()->with('success', 'Cart cleared!');
    }
}