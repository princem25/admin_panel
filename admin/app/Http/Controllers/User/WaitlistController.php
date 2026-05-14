<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Services\WaitlistService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WaitlistController extends Controller
{
    protected WaitlistService $waitlistService;

    public function __construct(WaitlistService $waitlistService)
    {
        $this->waitlistService = $waitlistService;
    }

    /**
     * Add the authenticated user to the product waitlist.
     * Only allowed when the product is inactive (out of stock).
     */
    public function join(Request $request, Product $product)
    {
        try {
            $result = $this->waitlistService->joinWaitlist($request->user(), $product);

            return back()->with($result['status'], $result['message']);
        } catch (\Exception $e) {
            Log::error('User\WaitlistController@join error', ['error' => $e->getMessage()]);
            throw $e;
        }
    }
}
