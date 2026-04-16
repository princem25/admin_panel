<?php

namespace App\Listeners\Admin;

use App\Events\Admin\ProductOutOfStock;
use App\Events\Admin\ProductRestocked;
use App\Events\Admin\ProductStockChanged;
use App\Events\Admin\ProductStockLow;
use App\Models\Product;
use Illuminate\Support\Facades\Log;

class DispatchInventoryEvents
{
    /**
     * Handle the ProductStockChanged event.
     *
     * This listener acts as a "gateway" — it inspects the old and new stock
     * values and conditionally fires the three inventory-specific events:
     *   - ProductStockLow   (stock < 10)
     *   - ProductOutOfStock  (stock == 0)
     *   - ProductRestocked   (old stock was 0, new stock > 0)
     */
    public function handle(ProductStockChanged $event): void
    {
        $product = Product::find($event->productId);

        if (!$product) {
            return;
        }

        $oldStock = $event->oldStock;
        $newStock = $event->newStock;

        // --- Restocked: was 0, now positive ---
        if ($oldStock === 0 && $newStock > 0) {
            Log::channel('products')->info('Inventory Gateway: ProductRestocked triggered', [
                'product_id' => $product->id,
                'old_stock'  => $oldStock,
                'new_stock'  => $newStock,
            ]);
            event(new ProductRestocked($product));
        }

        // --- Out of stock: stock just hit 0 ---
        if ($newStock === 0 && $oldStock > 0) {
            Log::channel('products')->warning('Inventory Gateway: ProductOutOfStock triggered', [
                'product_id' => $product->id,
                'old_stock'  => $oldStock,
            ]);
            event(new ProductOutOfStock($product));
        }

        // --- Low stock: below threshold, but not zero (and wasn't already below) ---
        if ($newStock > 0 && $newStock < 10) {
            Log::channel('products')->warning('Inventory Gateway: ProductStockLow triggered', [
                'product_id' => $product->id,
                'stock'      => $newStock,
            ]);
            event(new ProductStockLow($product));
        }
    }
}
