<?php

namespace App\Listeners\Customer;

use App\Events\Customer\ProductViewed;
use Illuminate\Support\Facades\Log;

class LogProductViewedEvent
{
    /**
     * Handle the event.
     *
     * @param  \App\Events\Customer\ProductViewed  $event
     * @return void
     */
    public function handle(ProductViewed $event)
    {
        $userId = $event->user ? $event->user->id : 'Guest';
        Log::info('Event: ProductViewed', [
            'product_id' => $event->product->id,
            'user_id' => $userId
        ]);
    }
}
