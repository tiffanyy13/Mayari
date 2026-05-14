<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Session\TokenMismatchException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'role' => \App\Http\Middleware\RoleMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (TokenMismatchException $e, Request $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Page expired. Refresh and try again.',
                ], 419);
            }

            if ($request->routeIs('logout')) {
                return redirect()
                    ->route('login')
                    ->with('warning', 'Your session had expired. Please sign in again.');
            }

            return redirect()
                ->back()
                ->withInput($request->except('password', 'password_confirmation', '_token'))
                ->with('warning', 'This page expired. Please try again.');
        });
    })->create();