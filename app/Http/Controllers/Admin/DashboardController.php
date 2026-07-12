<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\AcuerdoFirmado;
use App\Models\Representante;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use App\Models\Rol;
use Carbon\Carbon;
use App\Services\EstadisticasService;

class DashboardController extends Controller
{
    // Constructor al inicio: buena práctica y requerido por el IoC de Laravel
    public function __construct(
        protected EstadisticasService $estadisticasService
    ) {}

    public function index()
    {
        $user = Auth::user();

        // Lógica de redirección por roles:
        if ($user->esOperador()) {
            return redirect()->route('operador.dashboard');
        }

        // Si es Admin o Gerente, mostramos el dashboard principal
        $firmasHoy = AcuerdoFirmado::whereDate('fecha_firma', Carbon::today())->count();
        $totalClientes = Representante::count();
        $ultimosRegistros = AcuerdoFirmado::with('representante', 'participantes')
                                           ->latest('fecha_firma')
                                           ->limit(10)
                                           ->get();

        return view('admin.dashboard', compact('firmasHoy', 'totalClientes', 'ultimosRegistros'));
    }

    // Método operator() eliminado: su ruta apunta a OperadorController::index()
    // y era código muerto que duplicaba lógica de forma divergente.

    public function estadisticas()
    {
        return response()->json($this->estadisticasService->getDashboardData());
    }
}
