<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Context;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class RequestContextMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        
        $userType = 'guest';
        if ($user) {
            // Using the 'role' attribute derived from the User model (e.g., 'admin', 'user')
            $userType = $user->role ?? 'customer'; 
            if ($userType === 'user') {
                $userType = 'customer';
            }
        }

        $context = [
            'request_id' => (string) Str::uuid(),
            'user_id'    => $user ? $user->id : null,
            'user_type'  => $userType,
            'ip_address' => $request->ip(),
        ];

        // Store internally in request attributes so it's accessible anywhere
        foreach ($context as $key => $value) {
            $request->attributes->set($key, $value);
        }

        // Attach globally using modern Laravel 11+ Context so ALL channels receive it
        Context::add($context);
        
        // Also keep legacy Log::withContext strictly as a fallback for older channels
        Log::withContext($context);

        // Share globally with all views (including error views) to assist in debugging/tracing
        View::share('requestContext', $context);

        return $next($request);
    }
}
