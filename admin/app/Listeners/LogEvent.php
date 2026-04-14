<?php

namespace App\Listeners;

use App\Events\OrderPlaced;
use App\Events\OrderPaid;
use App\Events\OrderShipped;
use App\Events\OrderDelivered;
use Illuminate\Support\Facades\Log;

class LogEvent
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
    public function handle(OrderPlaced|OrderPaid|OrderShipped|OrderDelivered $event): void
    {
        $eventName = class_basename($event);
        $orderId = $event->order->id ?? 'unknown';
        
        Log::info("Event Logged: [{$eventName}] fired for order ID: {$orderId}");
        echo "Event Logged: [{$eventName}] fired for order ID: {$orderId}\n";
    }
}
