<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Support\Facades\Log;

class InvalidOrderException extends Exception
{
    public function render($request)
    {
        if ($request->expectsJson()) {
            return response()->json(['error' => 'Invalid order details provided'], 422);
        }

        return redirect()->back()->with('error', 'The order details provided are invalid.');
    }

    public function report()
    {
        Log::error('Invalid order exception triggered');
    }
}
