<?php

namespace App\Services\Support;

use App\Models\SupportTicket;
use App\Models\User;
use App\Notifications\NewSupportTicket;
use Illuminate\Support\Facades\Notification;

class SupportTicketService
{
    /**
     * Create a new support ticket and notify admins.
     *
     * @param array $data
     * @return SupportTicket
     */
    public function createTicket(array $data)
    {
        if (auth()->check()) {
            $data['user_id'] = auth()->id();
        }

        $ticket = SupportTicket::create($data);

        // Notify Admins (this will use the Bot Token since we returned a channel name in User.php)
        $admins = User::where('role', 'admin')->get();
        rescue(function () use ($admins, $ticket) {
            Notification::send($admins, new NewSupportTicket($ticket));
        });

        return $ticket;
    }
}
