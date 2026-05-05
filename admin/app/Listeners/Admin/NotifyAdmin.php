<?php

namespace App\Listeners\Admin;

use App\Events\Admin\OrderPlaced;
use App\Models\User;
use App\Notifications\NewOrderReceived;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class NotifyAdmin implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 3;

    /**
     * The number of seconds the job can run before timing out.
     *
     * @var int
     */
    public $timeout = 60;

    /**
     * Notify the admin of every order lifecycle stage.
     * Logs to the dedicated orders channel with structured context.
     */
    public function handle(OrderPlaced $event): void
    {
        $eventName = class_basename($event);
        $order = $event->order;

        Log::channel('admin')->info("Listener handled: NotifyAdmin ({$eventName})", [
            'event' => $eventName,
            'order_id' => $order->id ?? 'unknown',
            'customer_id' => $order->user_id ?? 'unknown',
            'total_amount' => $order->total ?? null,
            'status' => $order->status ?? null,
        ]);

        // Send notification to all admins
        $admins = User::where('is_admin', true)->get();
        Notification::send($admins, new NewOrderReceived($order));
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::channel('admin')->emergency("Background Listener CRITICAL: NotifyAdmin has failed after all retry attempts.", [
            'error' => $exception->getMessage()
        ]);
    }
}
