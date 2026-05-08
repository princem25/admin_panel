<?php

namespace App\Http\Controllers;

use App\Services\ProductService;

class HomeController extends Controller
{
    protected ProductService $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }

    /**
     * Show the application welcome page with featured products.
     */
    public function index()
    {
        $featuredProducts = $this->productService->getFeaturedProducts();

        return view('welcome', compact('featuredProducts'));
    }
}
