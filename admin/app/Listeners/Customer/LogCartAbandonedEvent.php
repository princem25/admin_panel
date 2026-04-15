<?php

namespace App\Listeners\Customer;

use App\Events\Customer\CartAbandoned;
use Illuminate\Support\Facades\Log;

class LogCartAbandonedEvent
{
    /**
     * Handle the event.
     *
     * @param  \App\Events\Customer\CartAbandoned  $event
     * @return void
     */
    public function handle(CartAbandoned $event)
    {
        Log::info('Event: CartAbandoned', [
            'cart_data' => $event->cart,
        ]);
    }
}
