<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSupportTicketRequest;
use App\Http\Requests\UpdateSupportTicketRequest;
use App\Services\Support\SupportTicketService;
use App\Models\SupportTicket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpKernel\Exception\HttpException;

class SupportTicketController extends Controller
{
    protected SupportTicketService $supportTicketService;

    public function __construct(SupportTicketService $supportTicketService)
    {
        $this->supportTicketService = $supportTicketService;
    }

    public function index()
    {
        try {
            $user = auth()->user();
            
            if ($user->role === 'admin') {
                $tickets = SupportTicket::latest()->get();
            } else {
                $tickets = SupportTicket::where('user_id', $user->id)->latest()->get();
            }
            
            return view('support.index', compact('tickets'));
        } catch (\Exception $e) {
            Log::error('SupportTicketController@index error', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    public function show(SupportTicket $supportTicket)
    {
        try {
            $user = auth()->user();

            // Check authorization
            if ($user->role !== 'admin' && $supportTicket->user_id !== $user->id) {
                abort(403);
            }

            return view('support.show', compact('supportTicket'));
        } catch (\Exception $e) {
            if ($e instanceof HttpException) {
                throw $e;
            }
            Log::error('SupportTicketController@show error', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    public function store(StoreSupportTicketRequest $request)
    {
        try {
            $this->supportTicketService->createTicket($request->validated());

            return back()->with('success', 'Support ticket submitted successfully!');
        } catch (\Exception $e) {
            Log::error('SupportTicketController@store error', ['error' => $e->getMessage()]);
            return back()->with('error', 'Failed to submit support ticket.');
        }
    }

    public function edit(SupportTicket $supportTicket)
    {
        try {
            if (auth()->user()->role !== 'admin') {
                abort(403);
            }

            if ($supportTicket->status === 'closed') {
                return redirect()->route('support.index')->with('error', 'Closed tickets cannot be edited.');
            }

            return view('support.edit', compact('supportTicket'));
        } catch (\Exception $e) {
            if ($e instanceof HttpException) {
                throw $e;
            }
            Log::error('SupportTicketController@edit error', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    public function update(UpdateSupportTicketRequest $request, SupportTicket $supportTicket)
    {
        try {
            if (auth()->user()->role !== 'admin') {
                abort(403);
            }

            if ($supportTicket->status === 'closed') {
                return redirect()->route('support.index')->with('error', 'Closed tickets cannot be edited.');
            }

            $supportTicket->update($request->validated());

            return redirect()->route('support.index')->with('success', 'Ticket updated successfully!');
        } catch (\Exception $e) {
            if ($e instanceof HttpException) {
                throw $e;
            }
            Log::error('SupportTicketController@update error', ['error' => $e->getMessage()]);
            return back()->with('error', 'Failed to update ticket.');
        }
    }
}
