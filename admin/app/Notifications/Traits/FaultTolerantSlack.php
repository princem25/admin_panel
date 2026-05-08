<?php

namespace App\Notifications\Traits;

use App\Exceptions\SlackRateLimitException;
use App\Notifications\SlackFallbackNotification;
use Illuminate\Support\Facades\Notification;
use stdClass;

trait FaultTolerantSlack
{
    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 3;

    /**
     * Get the backoff for retries.
     */
    public function backoff($exception = null)
    {
        if ($exception instanceof SlackRateLimitException) {
            return $exception->getRetryAfter();
        }

        return [10, 20, 30]; // default backoff
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception)
    {
        // Trigger fallback email after max tries
        $adminEmail = config('mail.from.address', 'admin@example.com'); 
        
        $dummyNotifiable = new stdClass();
        $dummyNotifiable->preferred_locale = config('app.locale');
        $dummyNotifiable->name = 'Admin';
        
        $details = [
            'type' => get_class($this),
            'reason' => $exception->getMessage(),
            'timestamp' => now()->toDateTimeString(),
            'payload' => method_exists($this, 'toArray') ? $this->toArray($dummyNotifiable) : [],
        ];

        Notification::route('mail', $adminEmail)
            ->notify(new SlackFallbackNotification($details));
    }
}
