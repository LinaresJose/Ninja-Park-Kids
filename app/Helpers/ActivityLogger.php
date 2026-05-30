<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class ActivityLogger
{
    /**
     * Registra una actividad del usuario en el sistema.
     *
     * @param string $action Descripción corta de la acción (ej. 'user_login', 'item_deleted')
     * @param string|null $description Descripción detallada opcional
     * @param array $extra Datos adicionales opcionales a registrar
     * @return void
     */
    public static function log(string $action, ?string $description = null, array $extra = [])
    {
        $user = Auth::user();
        
        $data = [
            'action' => $action,
            'description' => $description,
            'user_id' => $user ? $user->id : null,
            'user_email' => $user ? $user->correo : null, // Asumiendo que el campo es 'correo' basado en vistas anteriores
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
            'extra' => $extra,
        ];

        Log::channel('activity')->info("Activity: {$action}", $data);
    }
}
