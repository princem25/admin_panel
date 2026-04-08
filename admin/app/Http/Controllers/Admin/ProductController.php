<?php

namespace App\Http\Controllers\Admin;

use App\Events\ProductStockChanged;
use App\Exceptions\InsufficientPermissionException;
use App\Exceptions\InvalidOrderException;
use App\Exceptions\ProductOutOfStockException;
use App\Facades\Greeting;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Models\Category;
use App\Models\Product;
use App\Services\ProductService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    protected ProductService $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }

    /**
     * Invalidate product cache and related caches
     */
    private function invalidateProductCache(?int $productId = null)
    {
        // Clear specific product details
        if ($productId) {
            Cache::forget("product_{$productId}");
        }

        // Clear first 3 pages of listings
        for ($i = 1; $i <= 3; $i++) {
            Cache::forget("products_page_{$i}");
        }

        // Clear category caches
        Cache::forget('categories');
        Cache::forget('category_summary');

        // Clear featured products
        Cache::forget('featured_products');
    }

    public function index(Request $request)
    {
        // Log::debug — developer debug
        Log::debug('Product index requested', $request->only(['search', 'category', 'price']));

        $greeting = Greeting::greet('Product Section');
        $filters = $request->only(['search', 'category', 'price']);

        // Fetch products directly (simple caching requested for frontend only)
        $products = Product::filter($filters)
            ->latest()
            ->paginate(12)
            ->withQueryString();

        $total_products = $products->total();

        // Log::info
        Log::channel('products')->info('Admin viewed products list', ['total' => $total_products]);

        return view('product.index', compact(
            'products',
            'greeting',
            'total_products'
        ));
    }

    public function create()
    {
        $categories = Category::all();

        // Log::debug — developer debug
        Log::debug('Product create form accessed');

        return view('product.create', compact('categories'));
    }

    public function store(StoreProductRequest $request)
    {
        try {
            $data = $request->validated();

            // Log::debug — log validated request data for developer inspection
            Log::debug('Store product validated data', $data);

            if ($request->hasFile('file')) {
                $path = $request->file('file')->store('images', 'public');
                $data['image'] = basename($path);
            }

            if (isset($data['stock']) && $data['stock'] === 0) {
                Log::channel('products')->warning('New product created with zero stock', ['name' => $data['name']]);
            }

            $product = Product::create($data);

            $this->invalidateProductCache();

            Log::channel('products')->info('Product created successfully', [
                'id'   => $product->id,
                'name' => $product->name,
            ]);

            return redirect()->route('products.index')
                ->with('success', 'Product created!');
        } catch (QueryException $e) {
            // Log::alert — critical database issue
            Log::alert('Database error while creating product', ['error' => $e->getMessage()]);

            return back()->with('error', 'Database error. Please try again.');
        } catch (\Exception $e) {
            // Log::error — failed operation
            Log::error('Product creation failed', ['error' => $e->getMessage()]);

            return back()->with('error', 'Something went wrong!');
        }
    }

    public function show(Product $product)
    {
        try {
            // Session: track recently viewed products
            $recent = session()->get('recent', []);
            if (! in_array($product->id, $recent)) {
                session()->push('recent', $product->id);
            }

            // Log::info — normal action: product was viewed
            Log::channel('products')->info('Product viewed', ['id' => $product->id, 'name' => $product->name]);

            return view('product.index', compact('product'));
        } catch (ModelNotFoundException $e) {
            Log::error('Product not found in show', ['error' => $e->getMessage()]);
            return back()->with('error', 'Product not found.');
        }
    }

    public function edit(Product $product)
    {
        // Check stock: throw custom exception if product is out of stock and being edited
        if (isset($product->stock) && $product->stock <= 0) {
            // Log::warning — low/zero stock business warning
            Log::channel('products')->warning('Editing product with zero stock', ['id' => $product->id]);
        }

        $categories = Category::all();

        Log::debug('Product edit form accessed', ['product_id' => $product->id]);

        return view('product.edit', compact('product', 'categories'));
    }

    public function update(UpdateProductRequest $request, Product $product)
    {
        try {
            $data = $request->validated();

            Log::debug('Update product validated data', $data);

            if ($request->hasFile('file')) {
                if ($product->image && Storage::disk('public')->exists('images/' . $product->image)) {
                    Storage::disk('public')->delete('images/' . $product->image);
                }

                $path = $request->file('file')->store('images', 'public');
                $data['image'] = basename($path);
            }

            // Throw custom exception for records older than 1 year
            if ($product->created_at && $product->created_at->diffInDays(now()) > 365) {
                throw new InvalidOrderException('Products older than 1 year cannot be updated.');
            }

            $product->update($data);

            // Broadcast stock change if it was updated
            if (isset($data['stock'])) {
                event(new ProductStockChanged($product->id, $product->stock));
            }

            $this->invalidateProductCache($product->id);

            Log::channel('products')->info('Product updated successfully', ['id' => $product->id]);

            return redirect()->route('products.index')
                ->with('success', 'Updated!');
        } catch (InvalidOrderException $e) {
            // Log::warning — business logic violation
            Log::channel('security')->warning('Update blocked: ' . $e->getMessage(), ['product_id' => $product->id]);
            throw $e;
        } catch (QueryException $e) {
            // Log::alert — database-level alert
            Log::alert('Database error while updating product', ['error' => $e->getMessage()]);

            return back()->with('error', 'Database error. Please try again.');
        } catch (\Exception $e) {
            // Log::error — failed update
            Log::error('Product update failed', ['error' => $e->getMessage()]);

            return back()->with('error', 'Something went wrong!');
        }
    }

    public function destroy(Product $product)
    {
        try {
            $productId = $product->id;

            if ($product->image && Storage::disk('public')->exists('images/' . $product->image)) {
                Storage::disk('public')->delete('images/' . $product->image);
            }

            $product->delete();

            $this->invalidateProductCache($productId);

            // Log::warning — deletion is a significant action
            Log::channel('security')->warning('Product deleted', ['id' => $productId]);

            return redirect()->route('products.index')->with('success', 'Product deleted!');
        } catch (\Exception $e) {
            // Log::error
            Log::error('Product delete failed', ['error' => $e->getMessage()]);

            return back()->with('error', 'Could not delete product.');
        }
    }

    public function download(Product $product)
    {
        if (! $product->image || ! Storage::disk('public')->exists('images/' . $product->image)) {
            // Log::error — resource not found for download
            Log::error('Product image not found for download', ['product_id' => $product->id]);
            abort(404);
        }

        // Log::info — normal download action
        Log::info('Product image downloaded', ['product_id' => $product->id]);

        return Storage::disk('public')->download('images/' . $product->image);
    }

    /**
     * Demo: trigger a system-level emergency log (for testing purposes).
     * This would be called under catastrophic conditions (e.g., storage failure).
     */
    public function triggerEmergencyLog()
    {
        Log::emergency('CRITICAL: Product storage system is unreachable!');
        Log::alert('Alert: Product database connection intermittent.');

        return response()->json(['message' => 'Emergency log triggered. Check storage/logs/laravel.log.']);
    }

    public function export()
    {
        $callback = $this->productService->exportCsv();

        return response()->stream($callback, 200, [
            "Content-Type" => "text/csv",
            "Content-Disposition" => "attachment; filename=products.csv",
        ]);
    }

    }

