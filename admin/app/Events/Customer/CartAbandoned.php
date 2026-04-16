<?php

namespace App\Events\Customer;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class CartAbandoned
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $cart;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($cart)
    {
        $this->cart = $cart;

        Log::channel('customer')->warning('Event dispatched: CartAbandoned', [
            'item_count' => is_array($cart) ? count($cart) : 0,
            'cart_keys' => is_array($cart) ? array_keys($cart) : [],
        ]);
    }
}
