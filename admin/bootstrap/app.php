<?php

use App\Http\Middleware\RequestContextMiddleware;
use App\Http\Middleware\RequestLoggingMiddleware;
use App\Http\Middleware\roleMiddleware;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        channels: __DIR__.'/../routes/channels.php',
        health: '/up',
    )
    ->withBroadcasting(
        __DIR__.'/../routes/channels.php',
        ['middleware' => ['web', 'auth']],
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Appending to route groups guarantees they run AFTER StartSession & Auth middleware
        $middleware->web(append: [
            RequestContextMiddleware::class,
            RequestLoggingMiddleware::class,
        ]);

        $middleware->alias([
            'role' => roleMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->context(function () {
            $request = request();
            if ($request && $request->attributes->has('request_id')) {
                return [
                    'request_id' => $request->attributes->get('request_id'),
                    'user_id'    => $request->attributes->get('user_id'),
                    'user_type'  => $request->attributes->get('user_type'),
                    'ip_address' => $request->attributes->get('ip_address'),
                ];
            }
            return [];
        });
    })->create();
