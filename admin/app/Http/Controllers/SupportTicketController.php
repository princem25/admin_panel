<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSupportTicketRequest;
use App\Services\Support\SupportTicketService;
use App\Models\SupportTicket;
use Illuminate\Http\Request;

class SupportTicketController extends Controller
{
    protected SupportTicketService $supportTicketService;

    public function __construct(SupportTicketService $supportTicketService)
    {
        $this->supportTicketService = $supportTicketService;
    }

    public function index()
    {
        $user = auth()->user();
        
        if ($user->role === 'admin') {
            $tickets = SupportTicket::latest()->get();
        } else {
            $tickets = SupportTicket::where('user_id', $user->id)->latest()->get();
        }
        
        return view('support.index', compact('tickets'));
    }

    public function store(StoreSupportTicketRequest $request)
    {
        $this->supportTicketService->createTicket($request->validated());

        return back()->with('success', 'Support ticket submitted successfully!');
    }

    public function edit(SupportTicket $supportTicket)
    {
        if (auth()->user()->role !== 'admin') {
            abort(403);
        }

        if ($supportTicket->status === 'closed') {
            return redirect()->route('support.index')->with('error', 'Closed tickets cannot be edited.');
        }

        return view('support.edit', compact('supportTicket'));
    }

    public function update(Request $request, SupportTicket $supportTicket)
    {
        if (auth()->user()->role !== 'admin') {
            abort(403);
        }

        if ($supportTicket->status === 'closed') {
            return redirect()->route('support.index')->with('error', 'Closed tickets cannot be edited.');
        }

        $request->validate([
            'status' => 'required|string',
            'priority' => 'required|string',
        ]);

        $supportTicket->update($request->only('status', 'priority'));

        return redirect()->route('support.index')->with('success', 'Ticket updated successfully!');
    }
}
