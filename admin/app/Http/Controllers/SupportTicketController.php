<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSupportTicketRequest;
use App\Services\Support\SupportTicketService;

class SupportTicketController extends Controller
{
    protected SupportTicketService $supportTicketService;

    public function __construct(SupportTicketService $supportTicketService)
    {
        $this->supportTicketService = $supportTicketService;
    }

    public function store(StoreSupportTicketRequest $request)
    {
        $this->supportTicketService->createTicket($request->validated());

        return back()->with('success', 'Support ticket submitted successfully!');
    }
}
