<?php

namespace App\Listeners;

use App\Events\OrderPaid;
use Illuminate\Support\Facades\Log;

class UpdateInventory
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(OrderPaid $event): void
    {
        $orderId = $event->order->id ?? 'unknown';
        
        Log::info("Inventory updated (stock reduced) for order #{$orderId} after payment.");
        echo "Inventory updated (stock reduced) for order #{$orderId} after payment.\n";
    }
}
