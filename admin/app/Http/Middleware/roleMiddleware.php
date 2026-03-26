<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class roleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$role): Response
    {
        if (! $request->user()) {
            return redirect('/login');
        }

        if (! in_array($request->user()->role, $role)) {
            abort(403, 'unauthorized');
        }

        return $next($request);
    }
}
