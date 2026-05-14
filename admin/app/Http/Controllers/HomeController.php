<?php

namespace App\Http\Controllers;

use App\Services\ProductService;
use Illuminate\Support\Facades\Log;

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
        try {
            $featuredProducts = $this->productService->getFeaturedProducts();

            return view('welcome', compact('featuredProducts'));
        } catch (\Exception $e) {
            Log::error('HomeController@index error', ['error' => $e->getMessage()]);
            throw $e;
        }
    }
}
