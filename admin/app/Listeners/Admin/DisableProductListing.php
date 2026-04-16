<?php
namespace App\Listeners\Admin;

use App\Events\Admin\ProductOutOfStock;
use Illuminate\Support\Facades\Log;

class DisableProductListing
{
    /**
     * Automatically deactivates the product listing when stock reaches 0.
     */
    public function handle(ProductOutOfStock $event): void
    {
        $product = $event->product;

        Log::channel('products')->warning('Listener out of stock handled: DisableProductListing', [
            'product_id' => $product->id,
            'product_name' => $product->name,
        ]);
    }
}