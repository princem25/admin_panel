<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class HomeController extends Controller
{
    /**
     * Show the application welcome page with featured products.
     */
    public function index()
    {
        $featuredProducts = Cache::tags(['products'])->remember('featured_products', 3600, function () {
            return Product::with('category')
                ->latest()
                ->take(8)
                ->get();
        });

        return view('welcome', compact('featuredProducts'));
    }
}
