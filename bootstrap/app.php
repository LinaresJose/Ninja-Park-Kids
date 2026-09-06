<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Session\TokenMismatchException;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;

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
            // Inyecta headers de seguridad HTTP en todas las respuestas web
            \App\Http\Middleware\SecurityHeadersMiddleware::class,
        ]);

        // Excluir rutas públicas del CSRF para evitar el error 419 definitivamente
        $middleware->validateCsrfTokens(except: [
            '/consultar',               // Pantalla del cliente (cédula)
            '/registro/guardar',        // Guardar nuevo registro cliente
            '/registro/firma/*',        // Firma cliente recurrente
            'api/maloshy/*',            // Chatbot público
        ]);

        $middleware->trustProxies(at: '*');
    })
    ->withExceptions(function (Exceptions $exceptions): void {

        // --- Handler de sesión expirada (CSRF 419) ---
        $exceptions->render(function (TokenMismatchException $e, Request $request) {
            if ($request->wantsJson()) {
                return response()->json(['message' => 'Tu sesión ha expirado por inactividad. Por favor, recarga la página.'], 419);
            }
            return redirect()->back()->withInput($request->except('password', '_token'))->withErrors([
                'csrf' => 'Tu sesión ha expirado por inactividad. Por favor, intenta de nuevo.'
            ]);
        });

        // --- Handler de error de base de datos (Continuidad del servicio) ---
        // Si TiDB tiene un corte momentáneo, el sistema no arroja un error 500 crudo.
        // Redirige a una página amigable y registra el error en los logs para revisión.
        $exceptions->render(function (QueryException $e, Request $request) {
            Log::critical('[DB] Error de conexión o consulta', [
                'message' => $e->getMessage(),
                'sql'     => $e->getSql() ?? 'N/A',
                'ip'      => $request->ip(),
                'url'     => $request->fullUrl(),
            ]);

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'El sistema está procesando una alta demanda. Por favor, intenta de nuevo en unos segundos.',
                ], 503);
            }

            // Redirige de vuelta con un mensaje amigable en lugar de crashear
            return back()->withErrors([
                'sistema' => 'El sistema experimentó una interrupción momentánea. Por favor, intenta de nuevo en unos segundos. Si el problema persiste, comuníquese con el administrador.'
            ])->withInput();
        });

    })->create();
