<?php

namespace App\Listeners\Customer;

use App\Events\Customer\ProductAddedToCart;
use Illuminate\Support\Facades\Log;

class TrackProductAddedToCart
{
    /**
     * Handle the event.
     *
     * @param  \App\Events\Customer\ProductAddedToCart  $event
     * @return void
     */
    public function handle(ProductAddedToCart $event)
    {
        $userId = $event->user ? $event->user->id : 'Guest';
        Log::info("Product added to cart by user ID {$userId}", [
            'product_id' => $event->product->id,
            'product_name' => $event->product->name,
        ]);
    }
}
