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
        // Cache featured products for 1 hour (3600 seconds)
        // We define "featured" as the latest 8 products for now.
        $featuredProducts = Cache::remember('featured_products', 3600, function () {
            return Product::with('category')
                ->latest()
                ->take(8)
                ->get();
        });

        return view('welcome', compact('featuredProducts'));
    }
}
