<?php

namespace App\Http\Controllers;

use App\Models\SupportTicket;
use App\Models\User;
use App\Notifications\NewSupportTicket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;

class SupportTicketController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'subject' => 'required|string|max:255',
            'customer_name' => 'required|string|max:255',
            'priority' => 'required|in:low,medium,high',
            'message' => 'required|string',
        ]);

        $ticket = SupportTicket::create($validated);

        // Notify Admins (this will use the Bot Token since we returned a channel name in User.php)
        $admins = User::where('role', 'admin')->get();
        rescue(function () use ($admins, $ticket) {
            Notification::send($admins, new NewSupportTicket($ticket));
        });

        return back()->with('success', 'Support ticket submitted successfully!');
    }
}
