<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $products = Product::filter($request->only(['search', 'category', 'price']))
            ->with('category')
            ->latest()
            ->get();

        $cart = session()->get('cart', []);
        $cartProductIds = collect($cart)->pluck('product_id')->toArray();

        return view('user.products', compact('products', 'cartProductIds'));
    }
}
