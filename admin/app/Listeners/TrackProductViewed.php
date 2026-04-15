<?php

namespace App\Listeners;

use App\Events\ProductViewed;
use Illuminate\Support\Facades\Log;

class TrackProductViewed
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
        Log::info("Product viewed by user ID {$userId}", [
            'product_id' => $event->product->id,
            'product_name' => $event->product->name,
        ]);
    }
}
