<?php

use Illuminate\Support\Facades\Notification;
use App\Notifications\DailyDigest;
use App\Models\Order;
use App\Models\User;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Notifications\AnonymousNotifiable;

test('slack daily digest is sent', function () {
    Notification::fake();
    
    config(['services.slack.orders_webhook' => 'https://hooks.slack.com/services/orders']);

    // Create a user for yesterday
    $user = User::factory()->create(['created_at' => now()->subDay()]);

    // Create a category and product for yesterday
    $categoryId = DB::table('categories')->insertGetId([
        'name' => 'Test Category',
        'created_at' => now()->subDay(),
        'updated_at' => now()->subDay(),
    ]);

    $product = Product::create([
        'name' => 'Test Product',
        'slug' => 'test-product',
        'price' => 10,
        'stock' => 5,
        'description' => 'Test description',
        'category_id' => $categoryId,
        'created_at' => now()->subDay(),
        'updated_at' => now()->subDay(),
    ]);

    // Create orders for yesterday
    $order = Order::factory()->create([
        'created_at' => now()->subDay(),
        'total_amount' => 100,
        'status' => 'completed',
        'user_id' => $user->id
    ]);

    Order::factory()->create([
        'created_at' => now()->subDay(),
        'total_amount' => 50,
        'status' => 'pending',
        'user_id' => $user->id
    ]);

    // Create a failed job for yesterday
    DB::table('failed_jobs')->insert([
        'connection' => 'sync',
        'queue' => 'default',
        'payload' => '{}',
        'exception' => 'Test exception',
        'failed_at' => now()->subDay(),
        'uuid' => (string) str()->uuid(),
    ]);

    // Run the command
    $this->artisan('slack:daily-digest')
        ->assertExitCode(0);

    // Assert notification was sent
    Notification::assertSentOnDemand(
        DailyDigest::class,
        function ($notification, $channels, $notifiable) {
            return $notification->metrics['total_orders'] === 2
                && $notification->metrics['total_revenue'] == 150
                && $notification->metrics['new_customers'] === 1
                && $notification->metrics['low_stock_products'] === 1
                && $notification->metrics['failed_jobs'] === 1;
        }
    );
});

test('slack daily digest preview is sent to testing channel', function () {
    Notification::fake();

    // Set config values to ensure they are not null
    config(['services.slack.testing_webhook' => 'https://hooks.slack.com/services/test']);

    $this->artisan('slack:daily-digest --preview')
        ->assertExitCode(0);

    Notification::assertSentOnDemand(
        DailyDigest::class,
        function ($notification, $channels, $notifiable) {
            /** @var AnonymousNotifiable $notifiable */
            return $notifiable->routes['slack'] === 'https://hooks.slack.com/services/test';
        }
    );
});
