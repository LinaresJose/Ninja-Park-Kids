<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AuditLoggerMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Capturar el usuario autenticado antes de procesar la petición (útil para el logout)
        $userBefore = Auth::user();

        $response = $next($request);

        $method = $request->method();

        // Registrar mutaciones (POST, PUT, PATCH, DELETE)
        if (in_array($method, ['POST', 'PUT', 'PATCH', 'DELETE'])) {
            $user = Auth::user() ?: $userBefore;
            $userId = $user ? $user->id : 'Invitado';
            $userName = $user ? "{$user->nombre} {$user->apellido} ({$user->correo})" : 'Invitado';
            
            // Ocultar datos altamente sensibles del payload
            $payload = $request->except(['password', 'password_confirmation', 'firma_base64', '_token', 'token_qr']);
            $path = $request->path();
            
            // Registrar solo si la acción fue exitosa o de redirección lógica
            if ($response->getStatusCode() >= 200 && $response->getStatusCode() < 400) {
                Log::channel('audit')->info('Acción Crítica Ejecutada', [
                    'user_id'    => $userId,
                    'user'       => $userName,
                    'ip'         => $request->ip(),
                    'method'     => $method,
                    'path'       => $path,
                    'payload'    => $payload,
                    'status'     => $response->getStatusCode(),
                    'user_agent' => $request->userAgent(),
                ]);
            }
        }

        return $response;
    }
}
