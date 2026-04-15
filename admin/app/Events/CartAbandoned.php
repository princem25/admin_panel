<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

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
    }
}
