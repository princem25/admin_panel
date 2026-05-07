<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerifySlackSignature
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $signature = $request->header('X-Slack-Signature');
        $timestamp = $request->header('X-Slack-Request-Timestamp');
        $signingSecret = config('services.slack.signing_secret');

        if (!$signature || !$timestamp || !$signingSecret) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Prevent replay attacks by checking if the request is too old (e.g., > 5 minutes)
        if (abs(time() - $timestamp) > 300) {
            return response()->json(['error' => 'Request expired'], 403);
        }

        $baseString = "v0:{$timestamp}:" . $request->getContent();
        $expectedSignature = 'v0=' . hash_hmac('sha256', $baseString, $signingSecret);

        if (!hash_equals($expectedSignature, $signature)) {
            return response()->json(['error' => 'Invalid signature'], 403);
        }

        return $next($request);
    }
}
