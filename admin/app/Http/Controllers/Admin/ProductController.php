<?php

namespace App\Http\Controllers\Admin;

use App\Facades\Greeting;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Models\Category;
use App\Models\Product;
use App\Services\ProductService;
use Illuminate\Container\Attributes\Storage as AttributesStorage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    protected $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }

    public function index(Request $request)
    {
        $greeting = Greeting::greet('Product Section');

        if (! $request->hasAny(['search', 'category', 'price'])) {
            $products = Cache::remember('products_list', 60, function () {
                return collect($this->productService->all());
            });
        } else {
            $products = Product::filter($request->only(['search', 'category', 'price']))->latest()->get();
        }

        $total_products = collect($products)->count();

        return view('product.index', compact(
            'products',
            'greeting',
            'total_products'
        ));
    }

    public function create()
    {
        $categories = Category::all();

        return view('product.create', compact('categories'));
    }

    public function store(StoreProductRequest $request)
    {
        try {
            $data = $request->validated();

            if ($request->hasFile('file')) {
                // Returns path relative to disk root e.g. 'images/xyz.jpg'
                $path = $request->file('file')->store('images', 'public');
                // Store the filename only assuming we use asset('storage/images/...')
                $data['image'] = basename($path);
            }

            $product = Product::create($data);

            Cache::forget('products_list');

            Log::info('Product created', ['id' => $product->id]);

            return redirect()->route('products.index')
                ->with('success', 'Product created!');
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
        $categories = Category::all();

        return view('product.edit', compact('product', 'categories'));
    }

    public function update(UpdateProductRequest $request, Product $product)
    {
        try {
            $data = $request->validated();

            if ($request->hasFile('file')) {
                // Delete old image if it exists
                if ($product->image && Storage::disk('public')->exists('images/' . $product->image)) {
                    Storage::disk('public')->delete('images/' . $product->image);
                }

                $path = $request->file('file')->store('images', 'public');
                $data['image'] = basename($path);
            }

            $product->update($data);

            Cache::forget('products_list');

            Log::info('Product updated', ['id' => $product->id]);

            return redirect()->route('products.index')
                ->with('success', 'Updated!');
        } catch (\Exception $e) {
            Log::error('Product update failed', ['error' => $e->getMessage()]);

            return back()->with('error', 'Something went wrong!');
        }
    }

    public function destroy(Product $product)
    {
        if ($product->image && Storage::disk('public')->exists('images/' . $product->image)) {
            Storage::disk('public')->delete('images/' . $product->image);
        }

        $product->delete();

        Cache::forget('products_list');

        Log::warning('Product deleted', ['id' => $product->id]);

        return redirect()->route('products.index')->with('error', 'Deleted!!');
    }

    public function download(Product $product)
    {
        if (! $product->image || ! Storage::disk('public')->exists('images/' . $product->image)) {
            abort(404);
        }

        return Storage::disk('public')->download('images/' . $product->image);
    }
}
