<?php

namespace App\Listeners\Admin;


use App\Events\Admin\OrderStatusUpdated;
use App\Mail\OrderStatusEmail;
use App\Models\Order;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendEmailToCustomer implements ShouldQueue
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
     * Send (or simulate) a transactional email for every order lifecycle stage.
     * Logs to the dedicated orders channel with structured context.
     */
    public function handle(OrderStatusUpdated $event): void
    {
        $order = Order::find($event->orderId);
        $status = $event->orderStatus;

        if (!$order || !$order->user || !$order->user->email) {
            return;
        }



        try {
            // Sleep 5s to avoid Mailtrap rate limit
            sleep(5);
            Mail::to($order->user->email)->send(new OrderStatusEmail($order, $status, $order->user));

            Log::channel('customer')->info("Listener handled: SendEmailToCustomer (OrderStatusUpdated) sent email", [
                'event' => 'OrderStatusUpdated',
                'order_id' => $order->id,
                'customer_email' => $order->user->email,
                'status' => $status,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to send OrderStatusEmail', ['error' => $e->getMessage()]);
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::channel('customer')->emergency("Background Listener CRITICAL: SendEmailToCustomer has failed after all retry attempts.", [
            'error' => $exception->getMessage()
        ]);
    }
}
