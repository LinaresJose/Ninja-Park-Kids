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
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'role'  => \App\Http\Middleware\CheckRole::class,
            'audit' => \App\Http\Middleware\AuditLoggerMiddleware::class,
        ]);

        $middleware->web(append: [
            \App\Http\Middleware\SessionTimeout::class,
        ]);

        // Excluir rutas públicas del CSRF para evitar el error 419 definitivamente
        $middleware->validateCsrfTokens(except: [
            '/consultar',               // Pantalla del cliente (cédula)
            '/registro/guardar',        // Guardar nuevo registro cliente
            '/registro/firma/*',        // Firma cliente recurrente
            'api/malochy/*',            // Chatbot público
        ]);

        $middleware->trustProxies(at: '*');
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (TokenMismatchException $e, Request $request) {
            if ($request->wantsJson()) {
                return response()->json(['message' => 'Tu sesión ha expirado por inactividad. Por favor, recarga la página.'], 419);
            }
            return redirect()->back()->withInput($request->except('password', '_token'))->withErrors([
                'csrf' => 'Tu sesión ha expirado por inactividad. Por favor, intenta de nuevo.'
            ]);
        });
    })->create();
