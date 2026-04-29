<?php

namespace App\Listeners\Admin;

use App\Events\Admin\OrderPlaced;
use Illuminate\Support\Facades\Log;

class LogEvent
{
    /**
     * Handle the order lifecycle event.
     * Logs the OrderPlaced event to the dedicated orders channel.
     */
    public function handle(OrderPlaced $event): void
    {
        $eventName = class_basename($event);
        $order = $event->order;

        $context = [
            'event' => $eventName,
            'order_id' => $order->id ?? 'unknown',
            'customer_id' => $order->user_id ?? 'unknown',
            'total_amount' => $order->total ?? null,
            'status' => $order->status ?? null,
        ];

        Log::channel('orders')->info("Listener handled: LogEvent ({$eventName})", $context);
    }
}
