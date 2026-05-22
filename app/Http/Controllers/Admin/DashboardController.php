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

    public function users()
    {
        // Solo Gerente puede gestionar usuarios
        if (!Auth::user()->esGerente()) {
            return redirect()->route('admin.dashboard')->with('error', 'No tiene permisos para gestionar usuarios.');
        }

        $usuarios = User::with('rol')->get();
        $roles = Rol::orderBy('id')->get();
        return view('admin.users', compact('usuarios', 'roles'));
    }

    public function estadisticas()
    {
        // 1. Afluencia (7 días)
        $afluencia = DB::table('acuerdos_firmados')
            ->whereNotNull('fecha_firma')
            ->where('fecha_firma', '>=', Carbon::now()->subDays(6)->startOfDay())
            ->selectRaw('DATE(fecha_firma) as fecha, COUNT(id) as total')
            ->groupBy('fecha')
            ->orderBy('fecha', 'asc')
            ->get();

        // Para rellenar días sin ventas en la afluencia:
        $dias = collect();
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i)->format('Y-m-d');
            $dias[$date] = 0;
        }
        foreach ($afluencia as $a) {
            $dias[$a->fecha] = $a->total;
        }

        // Afluencia (Semana Anterior)
        $afluenciaAnt = DB::table('acuerdos_firmados')
            ->whereNotNull('fecha_firma')
            ->whereBetween('fecha_firma', [Carbon::now()->subDays(13)->startOfDay(), Carbon::now()->subDays(7)->endOfDay()])
            ->selectRaw('DATE(fecha_firma) as fecha, COUNT(id) as total')
            ->groupBy('fecha')
            ->orderBy('fecha', 'asc')
            ->get();

        $diasAnt = collect();
        for ($i = 6; $i >= 0; $i--) {
            $dateAnt = Carbon::now()->subDays($i + 7)->format('Y-m-d');
            $diasAnt[$dateAnt] = 0;
        }
        foreach ($afluenciaAnt as $a) {
            $diasAnt[$a->fecha] = $a->total;
        }

        Carbon::setLocale('es');
        $afluenciaLabels = collect($dias->keys())->map(function($d) {
            return ucfirst(Carbon::parse($d)->translatedFormat('D d'));
        })->values();
        
        $afluenciaData = $dias->values();
        $afluenciaDataAnt = $diasAnt->values();

        // 2. Horas Pico (Historial)
        $horas = DB::table('acuerdos_firmados')
            ->whereNotNull('fecha_firma')
            ->selectRaw('HOUR(fecha_firma) as hora, COUNT(id) as total')
            ->groupBy('hora')
            ->orderBy('hora', 'asc')
            ->get();
            
        $horasLabels = [];
        $horasData = [];
        foreach($horas as $h) {
            $horaStr = str_pad($h->hora, 2, '0', STR_PAD_LEFT) . ':00';
            $horasLabels[] = $horaStr;
            $horasData[] = $h->total;
        }

        // 3. Demografía (Historial completo de edades)
        $edades = DB::table('participantes')
            ->selectRaw('
                COALESCE(SUM(CASE WHEN TIMESTAMPDIFF(YEAR, fecha_nacimiento, CURDATE()) BETWEEN 0 AND 3 THEN 1 ELSE 0 END), 0) as "g1",
                COALESCE(SUM(CASE WHEN TIMESTAMPDIFF(YEAR, fecha_nacimiento, CURDATE()) BETWEEN 4 AND 7 THEN 1 ELSE 0 END), 0) as "g2",
                COALESCE(SUM(CASE WHEN TIMESTAMPDIFF(YEAR, fecha_nacimiento, CURDATE()) BETWEEN 8 AND 12 THEN 1 ELSE 0 END), 0) as "g3",
                COALESCE(SUM(CASE WHEN TIMESTAMPDIFF(YEAR, fecha_nacimiento, CURDATE()) > 12 THEN 1 ELSE 0 END), 0) as "g4"
            ')
            ->first();

        $demoData = [$edades->g1, $edades->g2, $edades->g3, $edades->g4];
        $totalDemo = array_sum($demoData);
        
        $demoLabels = ['0-3 años', '4-7 años', '8-12 años', '+13 años'];
        if ($totalDemo > 0) {
            $demoLabels = [
                '0-3 años (' . round(($edades->g1 / $totalDemo) * 100) . '%)',
                '4-7 años (' . round(($edades->g2 / $totalDemo) * 100) . '%)',
                '8-12 años (' . round(($edades->g3 / $totalDemo) * 100) . '%)',
                '+13 años (' . round(($edades->g4 / $totalDemo) * 100) . '%)'
            ];
        }

        return response()->json([
            'afluencia' => [
                'labels' => $afluenciaLabels, 
                'data' => $afluenciaData, 
                'data_ant' => $afluenciaDataAnt
            ],
            'horas' => ['labels' => $horasLabels, 'data' => $horasData],
            'demo' => ['labels' => $demoLabels, 'data' => $demoData]
        ]);
    }
}
