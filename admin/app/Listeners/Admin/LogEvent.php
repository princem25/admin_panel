<?php

namespace App\Listeners\Admin;

use App\Events\Admin\OrderDelivered;
use App\Events\Admin\OrderPaid;
use App\Events\Admin\OrderPlaced;
use App\Events\Admin\OrderShipped;
use Illuminate\Support\Facades\Log;

class LogEvent
{
    /**
     * Handle the order lifecycle event.
     * Logs all four order stages to the dedicated orders channel.
     */
    public function handle(OrderPlaced|OrderPaid|OrderShipped|OrderDelivered $event): void
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

        if ($event instanceof OrderDelivered) {
            Log::channel('orders')->warning("Listener handled: LogEvent ({$eventName})", $context);
            return;
        }

        Log::channel('orders')->info("Listener handled: LogEvent ({$eventName})", $context);
    }
}
