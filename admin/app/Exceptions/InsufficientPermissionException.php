<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Support\Facades\Log;

class InsufficientPermissionException extends Exception
{
    public function render($request)
    {
        if ($request->expectsJson()) {
            return response()->json(['error' => 'Insufficient permissions'], 403);
        }

        return redirect()->route('products.index')->with('error', 'You do not have permission to perform this action.');
    }

    public function report()
    {
        Log::warning('Insufficient permission exception triggered');
    }
}
