<?php

namespace App\Listeners\Admin;

use App\Events\Admin\OrderDelivered;
use App\Events\Admin\OrderPaid;
use App\Events\Admin\OrderPlaced;
use App\Events\Admin\OrderShipped;
use Illuminate\Support\Facades\Log;

class SendEmailToCustomer
{
    /**
     * Send (or simulate) a transactional email for every order lifecycle stage.
     * Logs to the dedicated orders channel with structured context.
     */
    public function handle(OrderPlaced|OrderPaid|OrderShipped|OrderDelivered $event): void
    {
        $eventName = class_basename($event);
        $order = $event->order;

        Log::channel('orders')->info("Listener handled: SendEmailToCustomer ({$eventName})", [
            'event' => $eventName,
            'order_id' => $order->id ?? 'unknown',
            'customer_id' => $order->user_id ?? 'unknown',
            'customer_email' => $order->user->email ?? 'unknown',
            'total_amount' => $order->total ?? null,
            'status' => $order->status ?? null,
        ]);
    }
}
