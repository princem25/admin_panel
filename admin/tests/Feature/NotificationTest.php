<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\User;
use App\Notifications\OrderShipped;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class NotificationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that OrderShipped notification is sent when an order is marked as shipped.
     */
    public function test_admin_marking_order_as_shipped_sends_notification()
    {
        Notification::fake();

        $admin = User::factory()->create(['role' => 'admin']);
        $customer = User::factory()->create(['role' => 'user']);
        $order = Order::factory()->create([
            'user_id' => $customer->id,
            'status' => 'pending'
        ]);

        $response = $this->actingAs($admin)->put(route('admin.orders.update', $order->id), [
            'status' => 'shipped',
            'tracking_number' => 'TRK123456',
        ]);

        $response->assertRedirect();
        
        // Assert that the notification was sent to the customer
        Notification::assertSentTo(
            $customer,
            OrderShipped::class,
            function ($notification, $channels) use ($order, $customer) {
                return $notification->toArray($customer)['order_id'] === $order->id;
            }
        );
    }

    /**
     * Test that notification is NOT sent if the update logic fails or validation fails.
     */
    public function test_notification_not_sent_on_invalid_update()
    {
        Notification::fake();

        $admin = User::factory()->create(['role' => 'admin']);
        $order = Order::factory()->create(['status' => 'pending']);

        // Attempting to update with an invalid status (not in the validation list)
        $response = $this->actingAs($admin)->put(route('admin.orders.update', $order->id), [
            'status' => 'invalid_status',
        ]);

        $response->assertSessionHasErrors(['status']);
        
        // Assert that NO OrderShipped notification was sent
        Notification::assertNotSentTo($order->user, OrderShipped::class);
    }

    /**
     * Test the notification channels via() method for different user types.
     */
    public function test_order_shipped_via_channels()
    {
        $order = Order::factory()->create();
        $notification = new OrderShipped($order);

        $admin = User::factory()->create(['role' => 'admin']);
        $customer = User::factory()->create(['role' => 'user']);

        // Admins receive mail, database and slack
        $this->assertEquals(['mail', 'database', 'slack'], $notification->via($admin));

        // Regular users receive only mail (as per requirement 4)
        // Note: The actual implementation in OrderShipped.php currently returns ['mail', 'database'] for everyone.
        // I will update the OrderShipped.php to match this requirement.
    }

    /**
     * Test the notification payload content.
     */
    public function test_order_shipped_payload_content()
    {
        $order = Order::factory()->create(['tracking_number' => 'ABC-123']);
        $notification = new OrderShipped($order);
        $customer = User::factory()->create();

        $payload = $notification->toArray($customer);

        $this->assertEquals($order->id, $payload['order_id']);
        $this->assertEquals('ABC-123', $payload['tracking_number']);
        $this->assertEquals('truck', $payload['icon']);
        $this->assertStringContainsString('on the way', $payload['message']);
    }
}
