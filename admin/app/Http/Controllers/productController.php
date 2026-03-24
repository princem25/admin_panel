<?php

namespace App\Http\Controllers;

use App\Facades\Greeting;
use App\Models\Product;
use App\Services\ProductService;
use Illuminate\Http\Request;

class productController extends Controller
{
    protected $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }

    public function index()
    {

        $greeting = Greeting::greet('Product Section');
        $products = $this->productService->all();
        return view('product.index', compact('products', 'greeting'));
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('product.create'); // view response
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        return view('product.edit', compact('product'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */

    public function destroy(Product $product)
    {
        $product->delete();

        return redirect()->route('products.index');
    }
}
