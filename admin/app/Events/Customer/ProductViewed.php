<?php

namespace App\Events\Customer;

use App\Models\Product;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ProductViewed
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
    }
}
