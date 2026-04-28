<?php

namespace App\Exceptions;

use Exception;

class ExternalApiException extends Exception
{
    /**
     * Render the exception into an HTTP response.
     */
    public function render($request)
    {
        if ($request->expectsJson()) {
            return response()->json([
                'success' => false,
                'message' => 'The external service is temporarily unavailable.',
            ], 503);
        }

        return redirect()->back()->with('error', 'The external service is temporarily unavailable. Please try again later.');
    }
}
