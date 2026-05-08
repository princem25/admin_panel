<?php

use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Notifications\DailyDigest;
use App\Notifications\SlackFallbackNotification;
use App\Exceptions\SlackRateLimitException;
use App\Notifications\Channels\FaultTolerantSlackChannel;

test('invalid webhook handling (sync) does not crash', function () {
    Http::fake([
        'https://hooks.slack.com/services/*' => Http::response('Invalid webhook', 500),
    ]);

    Log::shouldReceive('channel')
        ->with('slack')
        ->andReturnSelf()
        ->shouldReceive('error')
        ->atLeast()->once();

    $channel = new FaultTolerantSlackChannel();
        
    // Create a dummy notification that does NOT implement ShouldQueue
    $notification = new class extends \Illuminate\Notifications\Notification {
        public function toSlack($notifiable) { return 'test'; }
    };

    $notifiable = new class {
        public function routeNotificationFor($driver, $notification = null) { return 'https://hooks.slack.com/services/test'; }
    };

    $channel->send($notifiable, $notification);
});

test('429 Retry-After handling throws SlackRateLimitException', function () {
    Http::fake([
        'https://hooks.slack.com/services/*' => Http::response('Rate limited', 429, ['Retry-After' => '60']),
    ]);

    $channel = new FaultTolerantSlackChannel();
    
    // Create a dummy notification that implements ShouldQueue
    $notification = new class extends \Illuminate\Notifications\Notification implements \Illuminate\Contracts\Queue\ShouldQueue {
        public function toSlack($notifiable) { return 'test'; }
    };

    $notifiable = new class {
        public function routeNotificationFor($driver, $notification = null) { return 'https://hooks.slack.com/services/test'; }
    };

    try {
        $channel->send($notifiable, $notification);
        $this->fail('Expected SlackRateLimitException was not thrown');
    } catch (SlackRateLimitException $e) {
        expect($e->getRetryAfter())->toBe('60');
    }
});

test('fallback email triggering', function () {
    Notification::fake();

    $notification = new DailyDigest([]);
    
    $notification->failed(new \Exception('Test failure'));

    Notification::assertSentOnDemand(
        SlackFallbackNotification::class
    );
});
