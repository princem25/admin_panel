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
        Log::channel('customer')->info('Listener handled: LogProductViewedEvent', [
            'product_id' => $event->product->id,
            'product_name' => $event->product->name,
            'user_id' => $userId,
        ]);
    }
}
