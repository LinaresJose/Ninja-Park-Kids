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
    public function index()
    {
        $user = Auth::user();

        // Lógica de redirección por roles:
        if ($user->esOperador()) {
            return redirect()->route('operador.dashboard');
        }

        // Si es Admin o Gerente, mostramos el dashboard principal
        // Métricas básicas para el admin
        $firmasHoy = AcuerdoFirmado::whereDate('fecha_firma', Carbon::today())->count();
        $totalClientes = Representante::count();
        $ultimosRegistros = AcuerdoFirmado::with('representante', 'participantes')
                                           ->latest('fecha_firma')
                                           ->limit(10)
                                           ->get();

        return view('admin.dashboard', compact('firmasHoy', 'totalClientes', 'ultimosRegistros'));
    }

    public function operator()
    {
        // El operador solo necesita ver la lista de hoy y el botón de escaneo
        $firmasHoy = AcuerdoFirmado::with(['representante', 'participantes'])
                                   ->whereDate('fecha_firma', Carbon::today())
                                   ->latest()
                                   ->get();

        return view('admin.operator', compact('firmasHoy'));
    }

    protected $estadisticasService;

    public function __construct(EstadisticasService $estadisticasService)
    {
        $this->estadisticasService = $estadisticasService;
    }

    public function estadisticas()
    {
        return response()->json($this->estadisticasService->getDashboardData());
    }
}
