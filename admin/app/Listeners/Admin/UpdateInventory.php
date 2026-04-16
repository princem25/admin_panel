<?php

namespace App\Listeners\Admin;

use App\Events\Admin\OrderPaid;
use App\Events\Admin\ProductStockChanged;
use Illuminate\Support\Facades\Log;

class UpdateInventory
{
    /**
     * Reduce stock for all items in a paid order.
     * Logs to the dedicated orders channel with structured context.
     */
    public function handle(OrderPaid $event): void
    {
        $order = $event->order;

        Log::channel('orders')->info('Listener handled: UpdateInventory (Stock already managed in cart)', [
            'order_id' => $order->id ?? 'unknown',
            'customer_id' => $order->user_id ?? 'unknown',
            'item_count' => $order->items ? $order->items->count() : 0,
        ]);
    }
}
