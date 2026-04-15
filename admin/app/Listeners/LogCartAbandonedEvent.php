<?php

namespace App\Listeners;

use App\Events\CartAbandoned;
use Illuminate\Support\Facades\Log;

class LogCartAbandonedEvent
{
    /**
     * Handle the event.
     *
     * @param  \App\Events\CartAbandoned  $event
     * @return void
     */
    public function handle(CartAbandoned $event)
    {
        Log::info('Event: CartAbandoned', [
            'cart_data' => $event->cart,
        ]);
    }
}
