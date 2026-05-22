<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Tarifa;
use App\Models\Promocion;
use App\Models\HorarioParque;

class BotController extends Controller
{
    public function tarifas()
    {
        $tarifas = Tarifa::where('esta_activa', true)->get();
        return response()->json([
            'status' => 'success',
            'data' => $tarifas
        ]);
    }

    public function promociones()
    {
        $promociones = Promocion::where('esta_activa', true)
            ->whereDate('fecha_fin', '>=', now())
            ->whereDate('fecha_inicio', '<=', now())
            ->get();
            
        return response()->json([
            'status' => 'success',
            'data' => $promociones
        ]);
    }

    public function horarios()
    {
        $horarios = HorarioParque::orderBy('dia_semana')->get();
        return response()->json([
            'status' => 'success',
            'data' => $horarios
        ]);
    }
}
