<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use App\Models\User;
use Carbon\Carbon;

class ForgotPasswordController extends Controller
{
    /**
     * Mostrar el formulario para solicitar la recuperación de contraseña.
     */
    public function showLinkRequestForm()
    {
        return view('auth.passwords.email');
    }

    /**
     * Enviar el correo electrónico con el enlace de recuperación.
     */
    public function sendResetLinkEmail(Request $request)
    {
        $request->validate([
            'correo' => 'required|email|exists:usuarios,correo',
        ], [
            'correo.required' => 'El correo electrónico es requerido.',
            'correo.email' => 'Ingrese una dirección de correo válida.',
            'correo.exists' => 'No encontramos ningún usuario registrado con ese correo corporativo.',
        ]);

        $usuario = User::where('correo', $request->correo)->first();

        // Si el usuario existe pero está inactivo, denegar recuperación
        if (!$usuario || !$usuario->estado) {
            return back()->withErrors(['correo' => 'Esta cuenta se encuentra inactiva. Contacte al administrador.']);
        }

        // Generar un token seguro y único
        $token = Str::random(60);

        // Limpiar tokens anteriores para este correo
        DB::table('password_reset_tokens')->where('correo', $request->correo)->delete();

        // Almacenar el token en base de datos
        DB::table('password_reset_tokens')->insert([
            'correo' => $request->correo,
            'token' => $token,
            'created_at' => Carbon::now(),
        ]);

        // Generar enlace
        $link = route('password.reset', ['token' => $token, 'correo' => $request->correo]);

        // Enviar el correo usando la plantilla HTML premium
        try {
            Mail::send('emails.recuperar', ['link' => $link, 'nombre' => $usuario->nombre], function ($message) use ($request) {
                $message->to($request->correo);
                $message->subject('🔑 Restablecer acceso a Portal del Personal - Ninja Park');
            });
        } catch (\Throwable $e) {
            \Log::error('Fallo al enviar correo de recuperación: ' . $e->getMessage(), [
                'correo' => $request->correo,
            ]);
            // Limpiar el token para que el usuario pueda reintentar
            DB::table('password_reset_tokens')->where('correo', $request->correo)->delete();
            return back()->withErrors([
                'correo' => 'No pudimos enviar el correo de recuperación. Intente nuevamente en unos minutos.',
            ]);
        }

        return back()->with('success', '¡Enlace de recuperación enviado! Revisa tu bandeja de entrada corporativa y tu carpeta de spam.');
    }

    /**
     * Mostrar el formulario para restablecer la contraseña.
     */
    public function showResetForm(Request $request)
    {
        $token = $request->query('token');
        $correo = $request->query('correo');

        // Validar existencia de la solicitud
        $solicitud = DB::table('password_reset_tokens')
            ->where('correo', $correo)
            ->where('token', $token)
            ->first();

        if (!$solicitud) {
            return redirect()->route('password.request')
                ->withErrors(['correo' => 'El enlace de recuperación no es válido o ya ha sido utilizado.']);
        }

        // Validar expiración (60 minutos)
        if (Carbon::parse($solicitud->created_at)->addMinutes(60)->isPast()) {
            DB::table('password_reset_tokens')->where('correo', $correo)->delete();
            return redirect()->route('password.request')
                ->withErrors(['correo' => 'El enlace de recuperación ha expirado. Por favor, solicita uno nuevo.']);
        }

        return view('auth.passwords.reset', compact('token', 'correo'));
    }

    /**
     * Procesar el restablecimiento de la contraseña.
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'correo' => 'required|email|exists:usuarios,correo',
            'password' => 'required|string|min:8|confirmed',
        ], [
            'password.required' => 'La contraseña es obligatoria.',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
            'password.confirmed' => 'Las contraseñas no coinciden.',
        ]);

        // Validar token y correo
        $solicitud = DB::table('password_reset_tokens')
            ->where('correo', $request->correo)
            ->where('token', $request->token)
            ->first();

        if (!$solicitud) {
            return back()->withErrors(['correo' => 'El token de recuperación no es válido o ha expirado.']);
        }

        // Validar expiración (60 minutos)
        if (Carbon::parse($solicitud->created_at)->addMinutes(60)->isPast()) {
            DB::table('password_reset_tokens')->where('correo', $request->correo)->delete();
            return redirect()->route('password.request')
                ->withErrors(['correo' => 'El enlace de recuperación ha expirado. Por favor, solicita uno nuevo.']);
        }

        // Actualizar contraseña del usuario
        $usuario = User::where('correo', $request->correo)->first();
        if ($usuario) {
            $usuario->update([
                'password' => Hash::make($request->password)
            ]);
        }

        // Borrar el token para que no se pueda reutilizar
        DB::table('password_reset_tokens')->where('correo', $request->correo)->delete();

        // Invalidar la sesión actual y regenerar token CSRF para prevenir error 419 en el login
        $request->session()->flush();
        $request->session()->regenerate();

        return redirect()->route('login')->with('success', 'Contraseña restablecida con éxito. Ya puedes ingresar al Portal del Personal.');
    }
}
