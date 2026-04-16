<?php

namespace App\Listeners\Customer;

use App\Events\Customer\CartAbandoned;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class LogCartAbandonedEvent
{
    /**
     * Log the CartAbandoned event to the customer channel.
     */
    public function handle(CartAbandoned $event): void
    {
        $userId = Auth::id() ?? 'Guest';

        Log::channel('customer')->warning('Listener handled: LogCartAbandonedEvent', [
            'user_id'    => $userId,
            'item_count' => count($event->cart),
            'cart_keys'  => array_keys($event->cart),
        ]);
    }
}
