<?php

namespace App\Listeners\Admin;

use App\Events\Admin\OrderPlaced;
use App\Events\Admin\OrderPaid;
use App\Events\Admin\OrderShipped;
use App\Events\Admin\OrderDelivered;
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
