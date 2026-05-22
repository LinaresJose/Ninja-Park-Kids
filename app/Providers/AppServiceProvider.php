<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Detección dinámica de Ngrok para configurar el entorno sin tocar el .env
        $host = request()->getHost();
        $isNgrok = str_contains($host, 'ngrok-free.dev');

        if ($isNgrok) {
            // Forzar HTTPS y Cookies seguras para el túnel
            \Illuminate\Support\Facades\URL::forceScheme('https');
            config(['session.secure' => true]);
            config(['session.same_site' => 'none']);
            
            // Opcional: Forzar la URL base dinámicamente si es Ngrok
            config(['app.url' => 'https://' . $host]);
        } else {
            // Configuración segura para entorno local (HTTP)
            config(['session.secure' => false]);
            config(['session.same_site' => 'lax']);
        }
    }
}
