<?php

namespace App\Listeners\Customer;

use App\Events\Customer\ProductAddedToCart;
use Illuminate\Support\Facades\Log;

class LogProductAddedToCartEvent
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
        Log::info('Event: ProductAddedToCart', [
            'product_id' => $event->product->id,
            'user_id' => $userId
        ]);
    }
}
