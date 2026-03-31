<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Support\Facades\Log;

class ProductOutOfStockException extends Exception
{
    public function render($request)
    {
        if ($request->expectsJson()) {
            return response()->json([
                'error' => 'Product is out of stock'
            ], 400);
        }

        return redirect()->back()->with('error', 'Product is currently out of stock.');
    }

    public function report()
    {
        Log::warning('Product out of stock triggered');
    }
}
