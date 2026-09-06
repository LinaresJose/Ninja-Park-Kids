<?php

use App\Http\Controllers\RegistroController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ReportesController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes - Sistema Ninja Park
|--------------------------------------------------------------------------
*/

// Endpoint para renovar el token CSRF en silencio (evita error 419 en formularios abiertos por mucho tiempo)
Route::get('/csrf-token-refresh', function () {
    return response()->json(['token' => csrf_token()]);
})->name('csrf.refresh');

// 1. Pantalla Inicial: Donde el representante ingresa solo la cÃ©dula
Route::get('/', [RegistroController::class, 'verificarIndex'])->name('registro.verificar');

// 2. Procesar la Cédula: Busca en la base de datos si ya existe (100/min soporta múltiples tablets en el mismo Wi-Fi)
Route::post('/consultar', [RegistroController::class, 'consultarCedula'])->name('registro.consultar')->middleware('throttle:100,1');

// 3. Formulario para Nuevo Registro: Se activa si la cédula no existe
Route::get('/nuevo-registro/{cedula}', [RegistroController::class, 'index'])->name('registro.nuevo');

// 4. Guardar los datos del nuevo registro (30/min para picos de registros simultáneos en el parque)
Route::post('/registro/guardar', [RegistroController::class, 'store'])->name('registro.store')->middleware('throttle:30,1');

// 5. Formulario de Firma: Se activa si la cédula YA existe
Route::get('/registro/firma/{id}', [RegistroController::class, 'firma'])->name('registro.firma')->middleware('throttle:60,1');

// 6. Actualizar datos (por si añade niños) y Firmar (35/min para tráfico en sucursales)
Route::post('/registro/firma/{id}', [RegistroController::class, 'guardarFirma'])->name('registro.guardarFirma')->middleware('throttle:35,1');

// 7. Pantalla de Pase Exitoso
Route::get('/pase/{acuerdo_id}', [RegistroController::class, 'pase'])->name('registro.pase')->middleware('throttle:60,1');

// 8. Imagen del código QR generada en tiempo real (server-side)
Route::get('/pase/qr/{acuerdo_id}', [RegistroController::class, 'qrImage'])->name('registro.qr')->middleware('throttle:60,1');

// 9. Endpoint de validación para el Operador Integral (120/min soporta múltiples operadores escaneando)
Route::get('/validar/{token}', [RegistroController::class, 'validarPase'])->name('registro.validar')->middleware('throttle:120,1');


/*
|--------------------------------------------------------------------------
| GestiÃ³n Interna (Staff Only) - URL OFUSCADA
|--------------------------------------------------------------------------
*/

Route::prefix('staff-ninja')->middleware('audit')->group(function () {
    // Autenticación (Público dentro del prefijo)
    Route::get('/', [LoginController::class, 'showLoginForm'])->name('login');
    // Protección anti-fuerza-bruta: máximo 5 intentos por minuto por IP
    Route::post('/login', [LoginController::class, 'login'])->name('login.post')->middleware('throttle:5,1');
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    // --- RECUPERACIÓN DE CONTRASEÑA (Públicas dentro del prefijo) ---
    Route::get('/recuperar-password', [App\Http\Controllers\Auth\ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
    Route::post('/recuperar-password', [App\Http\Controllers\Auth\ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email')->middleware('throttle:3,1');
    Route::get('/restablecer-password', [App\Http\Controllers\Auth\ForgotPasswordController::class, 'showResetForm'])->name('password.reset');
    Route::post('/restablecer-password', [App\Http\Controllers\Auth\ForgotPasswordController::class, 'resetPassword'])->name('password.update')->middleware('throttle:3,1');

    // Rutas Protegidas por AUTH y ROLES (Nivel Operador Integral)
    Route::middleware(['auth', 'role:operador'])->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');

        // --- PANEL DE OPERADOR (CONTROL DE PUERTA) ---
        Route::get('/operador', [App\Http\Controllers\Staff\OperadorController::class, 'index'])->name('operador.dashboard');
        Route::get('/api/operador/buscar', [App\Http\Controllers\Staff\OperadorController::class, 'buscar'])->name('operador.buscar');
        Route::get('/api/operador/validar/{token}', [App\Http\Controllers\Staff\OperadorController::class, 'validarToken'])->name('operador.validar');
    });

    // Rutas exclusivas para ADMIN y GERENTE
    Route::middleware(['auth', 'role:admin'])->group(function () {
        Route::get('/api/estadisticas', [DashboardController::class, 'estadisticas'])->name('admin.estadisticas');

        // --- MÃ“DULO REPORTES Y DESCARGAS ---
        Route::get('/reportes', [ReportesController::class, 'index'])->name('admin.reportes');
        Route::get('/reportes/exportar', [ReportesController::class, 'exportarExcel'])->name('admin.reportes.exportar');
        Route::get('/reportes/exportar-pdf', [ReportesController::class, 'exportarPdf'])->name('admin.reportes.exportar_pdf');
        Route::get('/reportes/buscar-cliente', [ReportesController::class, 'buscarCliente'])->name('admin.reportes.buscar');
        Route::get('/reportes/pdf/{acuerdo_id}', [ReportesController::class, 'generarPdf'])->name('admin.reportes.pdf');
    });

    // Rutas exclusivas para GERENTE
    Route::middleware(['auth', 'role:gerente'])->group(function () {
        Route::get('/usuarios', [App\Http\Controllers\Admin\UserController::class, 'index'])->name('admin.users');
        Route::post('/usuarios', [App\Http\Controllers\Admin\UserController::class, 'store'])->name('staff.users.store');
        Route::put('/usuarios/{id}', [App\Http\Controllers\Admin\UserController::class, 'update'])->name('staff.users.update');
        Route::delete('/usuarios/{id}', [App\Http\Controllers\Admin\UserController::class, 'destroy'])->name('staff.users.destroy');

        // --- CONFIGURACIÓN DEL PARQUE ---
        // Legal
        Route::get('/legal', [App\Http\Controllers\Admin\LegalController::class, 'index'])->name('admin.legal.index');
        Route::post('/legal', [App\Http\Controllers\Admin\LegalController::class, 'store'])->name('admin.legal.store');
        Route::put('/legal/{id}', [App\Http\Controllers\Admin\LegalController::class, 'update'])->name('admin.legal.update');
        Route::post('/legal/{id}/activar', [App\Http\Controllers\Admin\LegalController::class, 'toggleActivo'])->name('admin.legal.activar');

        // Tarifas y Horarios
        Route::get('/tarifas', [App\Http\Controllers\Admin\TarifaController::class, 'index'])->name('admin.config.tarifas');
        Route::post('/tarifas', [App\Http\Controllers\Admin\TarifaController::class, 'store'])->name('admin.config.tarifas.store');
        Route::put('/tarifas/{id}', [App\Http\Controllers\Admin\TarifaController::class, 'update'])->name('admin.config.tarifas.update');
        Route::delete('/tarifas/{id}', [App\Http\Controllers\Admin\TarifaController::class, 'destroy'])->name('admin.config.tarifas.destroy');
        Route::post('/tarifas/{id}/toggle', [App\Http\Controllers\Admin\TarifaController::class, 'toggle'])->name('admin.config.tarifas.toggle');
        Route::put('/horarios', [App\Http\Controllers\Admin\TarifaController::class, 'updateHorarios'])->name('admin.config.horarios.update');

        // Promociones
        Route::get('/promociones', [App\Http\Controllers\Admin\PromocionController::class, 'index'])->name('admin.config.promociones');
        Route::post('/promociones', [App\Http\Controllers\Admin\PromocionController::class, 'store'])->name('admin.config.promociones.store');
        Route::put('/promociones/{id}', [App\Http\Controllers\Admin\PromocionController::class, 'update'])->name('admin.config.promociones.update');
        Route::delete('/promociones/{id}', [App\Http\Controllers\Admin\PromocionController::class, 'destroy'])->name('admin.config.promociones.destroy');
        Route::post('/promociones/{id}/toggle', [App\Http\Controllers\Admin\PromocionController::class, 'toggle'])->name('admin.config.promociones.toggle');
    });
});
// --- API BOT PÚBLICA ---
Route::prefix('api/bot')->middleware('throttle:30,1')->group(function () {
    Route::get('/tarifas', [App\Http\Controllers\Api\BotController::class, 'tarifas']);
    Route::get('/promociones', [App\Http\Controllers\Api\BotController::class, 'promociones']);
    Route::get('/horarios', [App\Http\Controllers\Api\BotController::class, 'horarios']);
});

// --- MALOSHY CHATBOT (Publico) ---
Route::prefix('api/maloshy')->middleware('throttle:60,1')->group(function () {
    Route::post('/chat', [App\Http\Controllers\Api\ChatbotController::class, 'chat'])->name('maloshy.chat');
    Route::post('/verificar', [App\Http\Controllers\Api\ChatbotController::class, 'verificarCedula'])->name('maloshy.verificar');
});

// --- MALOSHY CHATBOT (Staff) ---
Route::prefix('staff-ninja/api/maloshy')->middleware(['auth', 'role:operador'])->group(function () {
    Route::post('/staff', [App\Http\Controllers\Api\ChatbotController::class, 'chatStaff'])->name('maloshy.staff.chat');
});


