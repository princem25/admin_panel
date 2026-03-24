<?php

namespace App\Http\Controllers;

use App\Facades\Greeting;
use App\Http\Requests\formReq;
use App\Models\Product;
use App\Services\ProductService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

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

        // Cache products for 60 seconds
        $products = Cache::remember('products_list', 60, function () {
            return $this->productService->all();
        });

        // Log info
        Log::info('Products page visited');

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
    public function store(formReq $request)
    {
        DB::beginTransaction();

        try {
            $product = Product::create($request->validated());

            Log::info('Product created', ['id' => $product->id]);

            DB::commit();

            return redirect()->route('products.index')->with('success', 'Product created!');

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Product creation failed', ['error' => $e->getMessage()]);

            return back()->with('error', 'Something went wrong!');
        }
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
    public function update(formReq $request, Product $product)
    {
        DB::transaction(function () use ($request, $product) {
            $product->update($request->validated());
        });

        // Clear cache after update
        Cache::forget('products_list');

        Log::info('Product updated', ['id' => $product->id]);

        return redirect()->route('products.index')->with('success', 'Updated!');
    }

    /**
     * Remove the specified resource from storage.
     */

    public function destroy(Product $product)
    {
        // Delete file if exists
        if ($product->file && File::exists(storage_path('images/' . $product->file))) {
            File::delete(storage_path('images/' . $product->file));
        }

        $product->delete();

        // Clear cache
        Cache::forget('products_list');

        Log::warning('Product deleted', ['id' => $product->id]);

        return redirect()->route('products.index');
    }
}
