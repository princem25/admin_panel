<?php

namespace App\Observers;

use App\Models\Product;
use App\Events\Admin\ProductStockChanged;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class ProductObserver
{
    /**
     * Handle the Product "created" event.
     */
    public function created(Product $product): void
    {
        $this->invalidateCaches();
        
        Log::channel('products')->info('Observer: Product created', [
            'product_id' => $product->id,
            'name' => $product->name
        ]);
    }

    /**
     * Handle the Product "updated" event.
     */
    public function updated(Product $product): void
    {
        $this->invalidateCaches();

        // 🚀 Automate Stock Change Detection
        if ($product->wasChanged('stock')) {
            $oldStock = $product->getOriginal('stock');
            $newStock = $product->stock;

            Log::channel('products')->info('Observer: Stock change detected', [
                'product_id' => $product->id,
                'old_stock'  => $oldStock,
                'new_stock'  => $newStock
            ]);

            event(new ProductStockChanged($product->id, $newStock, $oldStock));
        }

        Log::channel('products')->info('Observer: Product updated', ['product_id' => $product->id]);
    }

    /**
     * Handle the Product "deleted" event.
     */
    public function deleted(Product $product): void
    {
        $this->invalidateCaches();

        Log::channel('products')->warning('Observer: Product deleted', ['product_id' => $product->id]);
    }

    /**
     * Clear all related product and administrative caches.
     */
    protected function invalidateCaches(): void
    {
        Cache::tags(['products', 'admin', 'customer'])->flush();
    }
}
