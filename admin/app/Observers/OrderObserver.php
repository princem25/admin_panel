<?php

namespace App\Observers;

use App\Models\Order;
use App\Events\Admin\OrderPlaced;
use App\Events\Admin\OrderStatusUpdated;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class OrderObserver
{
    /**
     * Handle the Order "created" event.
     */
    public function created(Order $order): void
    {
        // 🚀 Invalidate caches affects totals
        Cache::tags(['admin', 'customer'])->flush();

        Log::channel('orders')->info('Observer: Order created', ['order_id' => $order->id]);
        
        // NOTE: OrderPlaced is dispatched manually in CheckoutController 
        // after the items are created to ensure correct item counts in the broadcast.
    }

    /**
     * Handle the Order "updated" event.
     */
    public function updated(Order $order): void
    {
        // 🚀 Automate OrderStatusUpdated detection
        if ($order->wasChanged('status')) {
            // 🚀 Invalidate caches affected by status change (revenue, user orders)
            Cache::tags(['admin', 'customer'])->flush();

            Log::channel('orders')->info('Observer: Status change detected', [
                'order_id' => $order->id,
                'old_status' => $order->getOriginal('status'),
                'new_status' => $order->status
            ]);

            event(new OrderStatusUpdated($order->status, (string) $order->id));
        }
    }
}
