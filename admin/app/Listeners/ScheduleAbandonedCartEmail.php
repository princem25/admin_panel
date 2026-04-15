<?php

namespace App\Listeners;

use App\Events\CartAbandoned;
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
        Log::info('Abandoned cart email SCHEDULED.', ['cart' => $event->cart]);
        return now()->addSeconds(10); // 10 seconds for testing
    }

    /**
     * Handle the event.
     *
     * @param  \App\Events\CartAbandoned  $event
     * @return void
     */
    public function handle(CartAbandoned $event)
    {
        Log::info('Abandoned cart email EXECUTED by queue worker.', ['cart' => $event->cart]);
    }

    /**
     * Handle a job failure.
     */
    public function failed(CartAbandoned $event, \Throwable $exception)
    {
        //
    }
}
