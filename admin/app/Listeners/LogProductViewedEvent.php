<?php

namespace App\Listeners;

use App\Events\ProductViewed;
use Illuminate\Support\Facades\Log;

class LogProductViewedEvent
{
    /**
     * Handle the event.
     *
     * @param  \App\Events\ProductViewed  $event
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
