<?php

namespace App\Listeners\Customer;

use App\Events\Customer\ProductViewed;
use Illuminate\Support\Facades\Log;

class UpdateRecentlyViewed
{
    /**
     * Handle the event.
     *
     * @param  \App\Events\Customer\ProductViewed  $event
     * @return void
     */
    public function handle(ProductViewed $event)
    {
        Log::channel('customer')->info('Listener handled: UpdateRecentlyViewed', [
            'product_id' => $event->product->id,
            'product_name' => $event->product->name,
            'user_id' => $event->user?->id ?? 'Guest',
        ]);
    }
}
