<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class SessionTimeout
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $lastActivity = Session::get('last_activity');
            $timeout = config('session.lifetime') * 60; // Convert to seconds

            if ($lastActivity && (time() - $lastActivity > $timeout)) {
                Auth::logout();
                Session::flush();
                return redirect()->route('login')->withErrors(['correo' => 'Su sesión ha expirado por inactividad.']);
            }

            Session::put('last_activity', time());
        }

        return $next($request);
    }
}
