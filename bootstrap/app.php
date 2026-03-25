<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'not_suspended' => \App\Http\Middleware\EnsureNotSuspended::class,
            'role' => \App\Http\Middleware\CheckRole::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (\Symfony\Component\HttpKernel\Exception\HttpException $e, \Illuminate\Http\Request $request) {
            if ($request->expectsJson() || $request->is('api/*')) {
                $status = $e->getStatusCode();
                $message = $e->getMessage() ?: (\Symfony\Component\HttpFoundation\Response::$statusTexts[$status] ?? 'Error');

                return response()->json([
                    'error'   => 'HTTP_ERROR',
                    'message' => $message,
                    'errors'  => [],
                ], $status);
            }
        });
    })->create();
