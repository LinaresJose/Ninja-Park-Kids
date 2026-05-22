<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  $role
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Debe iniciar sesión para acceder al panel.');
        }

        $user = Auth::user();

        // Verificamos el acceso según el rol solicitado
        $tieneAcceso = false;

        switch ($role) {
            case 'gerente':
                $tieneAcceso = $user->esGerente();
                break;
            case 'admin':
                $tieneAcceso = $user->tieneAccesoAdmin();
                break;
            case 'operador':
                $tieneAcceso = $user->tieneAccesoOperador();
                break;
        }

        if (!$tieneAcceso) {
            // Si el usuario es cliente o no tiene el nivel necesario, lo mandamos al inicio público
            return redirect('/')->with('error', 'No tiene permisos para acceder a esta área.');
        }

        return $next($request);
    }
}
