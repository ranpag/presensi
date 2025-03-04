<?php

use App\Http\Middleware\IsAdmin;
use App\Http\Middleware\JwtMiddleware;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        channels: __DIR__.'/../routes/channels.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
            $middleware->alias([
                'admin' => IsAdmin::class,
                'auth.api' => JwtMiddleware::class
            ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (Throwable $exception) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan pada server.',
                'error' => $exception->getMessage(),
                'stack' => config('app.debug') ? $exception->getTrace() : [],
            ], 500);
        });
    })->create();
