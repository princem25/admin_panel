<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cart;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    //  View Cart
    public function index()
    {
        $cartItems = Cart::with('product')
            ->where('user_id', Auth::id())
            ->get();

        return view('cart.index', compact('cartItems'));
    }

    //  Add to Cart
    public function add(Product $product)
    {
        $cart = Cart::where('user_id', Auth::id())
            ->where('product_id', $product->id)
            ->first();

        if ($cart) {
            $cart->increment('quantity');
        } else {
            Cart::create([
                'user_id' => Auth::id(),
                'product_id' => $product->id,
                'quantity' => 1
            ]);
        }

        return back()->with('success', 'Product added to cart!');
    }

    //  Remove from Cart
    public function remove(Product $product)
    {
        Cart::where('user_id', Auth::id())
            ->where('product_id', $product->id)
            ->delete();

        return back()->with('success', 'Product removed from cart!');
    }

    //  Clear Cart
    public function clear()
    {
        Cart::where('user_id', Auth::id())->delete();

        return back()->with('success', 'Cart cleared!');
    }

 

public function increase($id)
{
    $cart = Cart::where('product_id', $id)
        ->where('user_id', Auth::id())
        ->first();

    if ($cart) {
        $cart->increment('quantity');
    }

    return back();
}

public function decrease($id)
{
    $cart = Cart::where('product_id', $id)
        ->where('user_id', Auth::id())
        ->first();

    if ($cart) {
        if ($cart->quantity > 1) {
            $cart->decrement('quantity');
        } else {
            $cart->delete(); // remove if 0
        }
    }

    return back();
}
}