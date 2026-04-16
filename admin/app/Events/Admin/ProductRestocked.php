<?php

namespace App\Events\Admin;

use App\Models\Product;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProductRestocked
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Product $product;

    /**
     * Create a new event instance.
     * Triggered when product stock increases from 0 to a positive value.
     */
    public function __construct(Product $product)
    {
        $this->product = $product;

        Log::channel('products')->info('Event dispatched: ProductRestocked', [
            'product_id' => $product->id,
            'product_name' => $product->name,
            'stock' => $product->stock,
        ]);
    }
}
