<?php

namespace App\Http\Controllers;

use App\Models\SupportTicket;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SlackInteractionController extends Controller
{
    /**
     * Handle Slack interactions (button clicks).
     */
    public function handle(Request $request)
    {
        $payload = json_decode($request->input('payload'), true);

        if (!$payload || !isset($payload['actions'])) {
            return response()->json(['error' => 'Invalid payload'], 400);
        }

        // The ticket ID is stored in the callback_id
        $callbackId = $payload['callback_id'] ?? '';
        $ticketId = str_replace('support_ticket_', '', $callbackId);
        
        // The action name is in the first element of the actions array
        $action = $payload['actions'][0];
        $actionType = $action['name'] ?? null;

        if (!$ticketId || !is_numeric($ticketId)) {
            return response()->json(['error' => 'Ticket ID missing or invalid in callback_id'], 400);
        }

        $ticket = SupportTicket::find($ticketId);
        if (!$ticket) {
            return response()->json(['error' => 'Ticket not found'], 404);
        }

        $responseMessage = "";
        $slackUser = $payload['user']['name'] ?? 'Someone';

        switch ($actionType) {
            case 'assign':
                // For this implementation, we'll assign to the first admin in the database
                // In a production app, you might map Slack user IDs to Laravel users
                $admin = User::where('role', 'admin')->first();
                if ($admin) {
                    $ticket->update(['assigned_user_id' => $admin->id]);
                    $responseMessage = "✅ Ticket #{$ticketId} assigned to {$admin->name} (Action by: {$slackUser})";
                } else {
                    $responseMessage = "❌ Could not find an admin to assign the ticket to.";
                }
                break;

            case 'in_progress':
                $ticket->update(['status' => 'in_progress']);
                $responseMessage = "🕒 Ticket #{$ticketId} marked as in progress (Action by: {$slackUser})";
                break;

            case 'close':
                $ticket->update(['status' => 'closed']);
                $responseMessage = "📁 Ticket #{$ticketId} closed successfully (Action by: {$slackUser})";
                break;

            default:
                $responseMessage = "⚠️ Unknown action: {$actionType}";
        }

        // Post confirmation message back to Slack thread
        if (isset($payload['response_url'])) {
            Http::post($payload['response_url'], [
                'text' => $responseMessage,
                'replace_original' => false, // Post as a new message to preserve history
            ]);
        }

        return response()->json([
            'text' => $responseMessage,
        ]);
    }
}
