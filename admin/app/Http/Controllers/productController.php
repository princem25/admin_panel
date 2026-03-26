<?php

namespace App\Http\Controllers;

use App\Facades\Greeting;
use App\Http\Requests\formReq;
use App\Models\Cart;
use App\Models\Category;
use App\Models\Product;
use App\Services\ProductService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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

    public function index(Request $request)
    {
        $greeting = Greeting::greet('Product Section');

        $search = $request->query('search');
        $category = $request->query('category');
        $price = $request->query('price');

        // Decide products
        if (!$search && !$category && !$price) {
            $products = Cache::remember('products_list', 60, function () {
                return $this->productService->all();
            });
        } else {
            $products = Product::where(function ($query) use ($search, $category, $price) {

                if (!empty($search)) {
                    $query->Where('name', 'like', '%' . $search . '%');
                }

                if (!empty($category)) {
                    $query->Where('category', $category);
                }

                if (!empty($price)) {
                    $query->Where('price', '<=', $price);
                }
            })->latest()->get();
        }

        // ✅ ALWAYS define
        $total_products = $products->count();

        return view('product.index', compact(
            'products',
            'greeting',
            'total_products'
        ));
    }

    public function create()
    {
        $categories = Category::all(); // fetch all categories

        return view('product.create', compact('categories'));
    }

    public function store(formReq $request)
    {
        try {
            $data = $request->validated();

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
        return view('product.edit', compact('product','categories'));
    }

    public function update(formReq $request, Product $product)
    {
        try {
            $data = $request->validated();

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

            return redirect()->route('products.index')
                ->with('success', 'Updated!');
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

        return redirect()->route('products.index')->with('error', 'Deleted!!');
    }

    public function download(Product $product)
    {

        $path = public_path('images/' . $product->image);

        if (!File::exists($path)) {
            abort(404);
        }

        return response()->download($path);
    }

    //------------------USER SIDE------------------//
    public function userProducts(Request $request)
    {
        $search = $request->query('search');
        $category = $request->query('category');
        $price = $request->query('price');

        $query = Product::query();

        if ($search) {
            $query->where('name', 'like', "%$search%");
        }

        if ($category) {
            $query->where('category', $category);
        }

        if ($price) {
            $query->where('price', '<=', $price);
        }

        $products = $query->latest()->get();

        $cartProductIds = Cart::where('user_id', Auth::id())
            ->pluck('product_id')
            ->toArray();

        return view('user.products', compact('products', 'cartProductIds'));
    }
}
