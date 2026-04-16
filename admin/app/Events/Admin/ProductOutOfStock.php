<?php

namespace App\Events\Admin;

use App\Models\Product;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProductOutOfStock
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Product $product;

    /**
     * Create a new event instance.
     * Triggered when product stock reaches exactly 0.
     */
    public function __construct(Product $product)
    {
        $this->product = $product;

        Log::channel('products')->warning('Event dispatched: ProductOutOfStock', [
            'product_id' => $product->id,
            'product_name' => $product->name,
            'stock' => $product->stock,
        ]);
    }
}
