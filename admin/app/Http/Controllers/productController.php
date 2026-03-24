<?php

namespace App\Http\Controllers;

use App\Facades\Greeting;
use App\Models\Product;
use App\Services\ProductService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class ProductController extends Controller
{
    protected $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }

    public function index()
    {
        $greeting = Greeting::greet('Product Section');

        $products = Cache::remember('products_list', 60, function () {
            return $this->productService->all();
        });

        Log::info('Products page visited');

        return view('product.index', compact('products', 'greeting'));
    }

    public function create()
    {
        return view('product.create');
    }

    public function store(Request $request)
    {
       
        // Validation
        $request->validate([
            'name' => 'required',
            'price' => 'required|numeric',
            'description' => 'required',
            'file' => 'required|image|mimes:jpg,jpeg,png|max:2048'
        ]);

        try {

            $data = $request->only(['name', 'price', 'description']);

            // Image upload
            if ($request->hasFile('file')) {
                $file = $request->file('file');
                $filename = time() . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('images'), $filename);
                $data['image'] = $filename;
            }

            $product = Product::create($data);

            Cache::forget('products_list');

            Log::info('Product created', ['id' => $product->id]);

             

            return redirect()->route('products.index')->with('success', 'Product created!');

        } catch (\Exception $e) {
            

            Log::error('Product creation failed', ['error' => $e->getMessage()]);

            return back()->with('error', 'Something went wrong!');
        }
    }

    public function show(Product $product)
    {
        return view('product.show', compact('product'));
    }

    public function edit(Product $product)
    {
        return view('product.edit', compact('product'));
    }

    public function update(Request $request, Product $product)
    {
         
        // Validation
        $request->validate([
            'name' => 'required',
            'price' => 'required|numeric',
            'description' => 'required',
            'file' => 'nullable|image|mimes:jpg,jpeg,png|max:2048'
        ]);
        try {

            $data = $request->only(['name', 'price', 'description']);

            // If new image uploaded
            if ($request->hasFile('file')) {

                // Delete old image
                if ($product->image && File::exists(public_path('images/' . $product->image))) {
                    File::delete(public_path('images/' . $product->image));
                }

                // Upload new image
                $file = $request->file('file');
                $filename = time() . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('images'), $filename);

                $data['image'] = $filename;
            }

            $product->update($data);

            Cache::forget('products_list');

            Log::info('Product updated', ['id' => $product->id]);

            return redirect()->route('products.index')->with('success', 'Updated!');

        } catch (\Exception $e) { 
            
            Log::error('Product update failed', ['error' => $e->getMessage()]);

            return back()->with('error', 'Something went wrong!');
        }
    }

    public function destroy(Product $product)
    {
        // Delete image if exists
        if ($product->image && File::exists(public_path('images/' . $product->image))) {
            File::delete(public_path('images/' . $product->image));
        }

        $product->delete();

        Cache::forget('products_list');

        Log::warning('Product deleted', ['id' => $product->id]);

        return redirect()->route('products.index');
    }
}