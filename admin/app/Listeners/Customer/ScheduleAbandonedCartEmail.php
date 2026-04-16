<?php

namespace App\Listeners\Customer;

use App\Events\Customer\CartAbandoned;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class ScheduleAbandonedCartEmail implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Set the delay for the queued listener.
     */
    public function withDelay(CartAbandoned $event)
    {
        Log::channel('customer')->info('Listener scheduled: ScheduleAbandonedCartEmail', [
            'item_count' => count($event->cart),
            'cart_keys' => array_keys($event->cart),
            'delay_seconds' => 10,
        ]);
        return now()->addSeconds(10); // 10 seconds for testing
    }

    /**
     * Handle the event.
     *
     * @param  \App\Events\Customer\CartAbandoned  $event
     * @return void
     */
    public function handle(CartAbandoned $event)
    {
        Log::channel('customer')->info('Listener handled: ScheduleAbandonedCartEmail', [
            'item_count' => count($event->cart),
            'cart_keys' => array_keys($event->cart),
        ]);
    }

    /**
     * Handle a job failure.
     */
    public function failed(CartAbandoned $event, \Throwable $exception)
    {
        Log::channel('customer')->error('Listener failed: ScheduleAbandonedCartEmail', [
            'item_count' => count($event->cart),
            'cart_keys' => array_keys($event->cart),
            'error' => $exception->getMessage(),
        ]);
    }
}
