<?php

namespace App\Events\Customer;

use App\Models\Product;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProductAddedToCart
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $product;
    public $user;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Product $product, $user = null)
    {
        $this->product = $product;
        $this->user = $user;

        Log::channel('customer')->info('Event dispatched: ProductAddedToCart', [
            'product_id' => $product->id,
            'product_name' => $product->name,
            'user_id' => $user?->id ?? 'Guest',
        ]);
    }
}
