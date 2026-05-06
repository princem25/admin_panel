<?php

namespace App\Http\Controllers\Admin;

use App\Events\Admin\ProductStockChanged;
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

            if (filled($data['stock'] ?? null) && $data['stock'] === 0) {
                Log::channel('products')->warning('New product created with zero stock', ['name' => $data['name']]);
            }

            $product = tap(Product::create($data), function ($product) {
                Log::info('Model created/updated', ['id' => $product->id]);
            });

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

            $product = tap($product)->update($data);

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
        try {
            $filename = 'products_report_' . date('Y-m-d') . '.csv';
            $disk = Storage::disk('reports');

            // 1 & 2: Check if file exists in the reports disk
            if ($disk->exists($filename)) {
                return $disk->download($filename);
            }

            // 3: File does not exist, generate and store
            $csvContent = $this->productService->getCsvString();
            $disk->put($filename, $csvContent);

            return $disk->download($filename);

        } catch (\Exception $e) {
            Log::error('Product export failed', ['error' => $e->getMessage()]);
            return back()->with('error', 'Export failed.');
        }
    }
}
