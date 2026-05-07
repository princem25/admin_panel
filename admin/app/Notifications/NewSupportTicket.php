<?php

namespace App\Notifications;

use App\Models\SupportTicket;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\SlackMessage;
use Illuminate\Notifications\Notification;

class NewSupportTicket extends Notification implements ShouldQueue
{
    use Queueable;

    public $ticket;

    /**
     * Create a new notification instance.
     */
    public function __construct(SupportTicket $ticket)
    {
        $this->ticket = $ticket;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['slack'];
    }

    /**
     * Get the Slack representation of the notification.
     */
    public function toSlack(object $notifiable): SlackMessage
    {
        return (new SlackMessage)
            ->from('Support Bot', ':lifesaver:')
            ->to('#support')
            ->content("🛟 New Support Ticket")
            ->attachment(function ($attachment) {
                $attachment->title("Ticket: {$this->ticket->subject}")
                    ->callbackId("support_ticket_{$this->ticket->id}")
                    ->fields([
                        'Customer' => $this->ticket->customer_name,
                        'Priority' => ucfirst($this->ticket->priority),
                        'Ticket ID' => "#{$this->ticket->id}",
                    ])
                    ->fallback("New support ticket: {$this->ticket->subject}")
                    ->action('Assign to me', 'assign', 'default')
                    ->action('Mark in progress', 'in_progress', 'primary')
                    ->action('Close', 'close', 'danger');
            });
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'ticket_id' => $this->ticket->id,
            'subject' => $this->ticket->subject,
            'customer_name' => $this->ticket->customer_name,
            'priority' => $this->ticket->priority,
        ];
    }
}
