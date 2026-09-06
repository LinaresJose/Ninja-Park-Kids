<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * SecurityHeadersMiddleware
 *
 * Inyecta cabeceras HTTP de seguridad en todas las respuestas.
 * Protege contra: Clickjacking, XSS reflejado, MIME sniffing,
 * fugas de informacion en Referer, y uso indebido de la camara desde dominios externos.
 */
class SecurityHeadersMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Evita que la app sea embebida en iframes de otros dominios (anti-Clickjacking)
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');

        // Impide que el navegador adivine el tipo de contenido (anti-MIME sniffing)
        $response->headers->set('X-Content-Type-Options', 'nosniff');

        // Proteccion XSS basica en navegadores antiguos
        $response->headers->set('X-XSS-Protection', '1; mode=block');

        // Controla que informacion de URL se envia al hacer referencia a otros sitios
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');

        // Limita el acceso a hardware del dispositivo:
        // - camera: solo el propio dominio (para el escaner QR del operador)
        // - microphone/geolocation: nadie
        $response->headers->set('Permissions-Policy', 'camera=(self), microphone=(), geolocation=()');

        return $response;
    }
}
