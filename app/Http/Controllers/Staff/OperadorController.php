<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\AcuerdoFirmado;
use Illuminate\Http\Request;
use Carbon\Carbon;

class OperadorController extends Controller
{
    /**
     * Vista principal del panel de operador.
     * Carga por defecto los registros de las últimas 24 horas.
     */
    public function index()
    {
        $hace24h = Carbon::now()->subHours(24);
        
        $registros = AcuerdoFirmado::with(['representante', 'participantes'])
            ->where('fecha_firma', '>=', $hace24h)
            ->orderBy('fecha_firma', 'desc')
            ->get();

        return view('staff.operador.dashboard', compact('registros'));
    }

    /**
     * Búsqueda avanzada / fuzzy.
     * Amplía la ventana de búsqueda a los últimos 15 días.
     */
    public function buscar(Request $request)
    {
        $query = $request->input('q');
        $fecha = $request->input('fecha');
        $hace15dias = Carbon::now()->subDays(15);

        $resultados = AcuerdoFirmado::with(['representante', 'participantes'])
            ->when($fecha, function($q) use ($fecha) {
                return $q->whereDate('fecha_firma', $fecha);
            }, function($q) use ($hace15dias) {
                return $q->where('fecha_firma', '>=', $hace15dias);
            })
            ->where(function($q) use ($query) {
                if (!$query) return;
                $q->whereHas('representante', function($qr) use ($query) {
                    $qr->where('nombre', 'LIKE', "%{$query}%")
                       ->orWhere('apellido', 'LIKE', "%{$query}%")
                       ->orWhere('cedula', 'LIKE', "%{$query}%");
                })
                ->orWhereHas('participantes', function($qp) use ($query) {
                    $qp->where('nombre', 'LIKE', "%{$query}%")
                       ->orWhere('apellido', 'LIKE', "%{$query}%");
                })
                ->orWhere('token_qr', 'LIKE', "%{$query}%");
            })
            ->orderBy('fecha_firma', 'desc')
            ->get();

        // Mapeamos para enviar un JSON limpio a Alpine.js
        $data = $resultados->map(function($acc) {
            return [
                'id' => $acc->id,
                'token' => $acc->token_qr,
                'fecha' => Carbon::parse($acc->fecha_firma)->format('d/m/Y H:i'),
                'representante' => $acc->representante->nombre . ' ' . $acc->representante->apellido,
                'cedula' => $acc->representante->cedula,
                'telefono' => $acc->representante->telefono,
                'niños' => $acc->participantes->pluck('nombre')->toArray(),
                'status'        => Carbon::parse($acc->fecha_firma)->isToday() ? '✅ Vigente' : '🔴 Expirado'
            ];
        });

        return response()->json($data);
    }

    /**
     * Validación rápida de un Token QR.
     */
    public function validarToken($token)
    {
        $acuerdo = AcuerdoFirmado::with(['representante', 'participantes'])
            ->where('token_qr', $token)
            ->first();

        if (!$acuerdo) {
            return response()->json(['success' => false, 'message' => 'Código no encontrado'], 404);
        }

        $vigente = Carbon::parse($acuerdo->fecha_firma)->isToday();

        return response()->json([
            'success'       => true,
            'representante' => $acuerdo->representante->nombre . ' ' . $acuerdo->representante->apellido,
            'cedula'        => $acuerdo->representante->cedula,
            'telefono'      => $acuerdo->representante->telefono,
            'niños'         => $acuerdo->participantes->map(function($p) {
                return [
                    'nombre' => $p->nombre . ' ' . $p->apellido,
                    'edad'   => Carbon::parse($p->fecha_nacimiento)->age
                ];
            }),
            'fecha_firma'   => Carbon::parse($acuerdo->fecha_firma)->format('d/m/Y H:i'),
            'vigente'       => $vigente,
            'status'        => $vigente ? '✅ Vigente' : '🔴 Expirado — Pase de otro día',
        ], $vigente ? 200 : 422);
    }
}
