<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Tarifa;
use App\Models\Promocion;
use App\Models\HorarioParque;

class ConfigController extends Controller
{
    // --- TARIFAS Y HORARIOS ---
    public function tarifas()
    {
        $tarifas = Tarifa::all();
        $horarios = HorarioParque::orderBy('dia_semana')->get();
        
        // Si no hay horarios, crear la semana vacía
        if ($horarios->isEmpty()) {
            for ($i = 0; $i <= 6; $i++) {
                HorarioParque::create(['dia_semana' => $i]);
            }
            $horarios = HorarioParque::orderBy('dia_semana')->get();
        }

        return view('admin.config.tarifas', compact('tarifas', 'horarios'));
    }

    public function storeTarifa(Request $request)
    {
        $request->validate([
            'nombre_tarifa' => 'required|string',
            'duracion_minutos' => 'nullable|integer',
            'precio' => 'required|numeric',
        ]);

        Tarifa::create([
            'nombre_tarifa' => $request->nombre_tarifa,
            'duracion_minutos' => $request->duracion_minutos,
            'precio' => $request->precio,
            'esta_activa' => $request->has('esta_activa')
        ]);

        return redirect()->route('admin.config.tarifas')->with('success', 'Tarifa añadida correctamente.');
    }

    public function updateTarifa(Request $request, $id)
    {
        $tarifa = Tarifa::findOrFail($id);
        
        $request->validate([
            'nombre_tarifa' => 'required|string',
            'duracion_minutos' => 'nullable|integer',
            'precio' => 'required|numeric',
        ]);

        $tarifa->update([
            'nombre_tarifa' => $request->nombre_tarifa,
            'duracion_minutos' => $request->duracion_minutos,
            'precio' => $request->precio,
        ]);
        return redirect()->route('admin.config.tarifas')->with('success', 'Tarifa actualizada correctamente.');
    }

    public function destroyTarifa($id)
    {
        $tarifa = Tarifa::findOrFail($id);
        $tarifa->delete();
        return redirect()->route('admin.config.tarifas')->with('success', 'Tarifa eliminada correctamente.');
    }

    public function toggleTarifa($id)
    {
        $tarifa = Tarifa::findOrFail($id);
        $tarifa->esta_activa = !$tarifa->esta_activa;
        $tarifa->save();
        return redirect()->back()->with('success', 'Estado de tarifa actualizado.');
    }

    public function updateHorarios(Request $request)
    {
        $horarios = $request->input('horarios', []);
        
        foreach ($horarios as $id => $data) {
            $horario = HorarioParque::find($id);
            if ($horario) {
                $horario->update([
                    'hora_apertura' => $data['hora_apertura'] ?? null,
                    'hora_cierre' => $data['hora_cierre'] ?? null,
                    'esta_cerrado' => isset($data['esta_cerrado']) ? true : false,
                ]);
            }
        }
        return redirect()->route('admin.config.tarifas')->with('success', 'Horarios actualizados correctamente.');
    }

    // --- PROMOCIONES ---
    public function promociones()
    {
        $promociones = Promocion::orderBy('fecha_fin', 'desc')->get();
        return view('admin.config.promociones', compact('promociones'));
    }

    public function storePromocion(Request $request)
    {
        $request->validate([
            'titulo' => 'required|string',
            'descripcion_detallada' => 'nullable|string',
            'precio_especial' => 'required|numeric',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after_or_equal:fecha_inicio',
        ]);

        Promocion::create([
            'titulo' => $request->titulo,
            'descripcion_detallada' => $request->descripcion_detallada,
            'precio_especial' => $request->precio_especial,
            'fecha_inicio' => $request->fecha_inicio,
            'fecha_fin' => $request->fecha_fin,
            'esta_activa' => $request->has('esta_activa')
        ]);

        return redirect()->route('admin.config.promociones')->with('success', 'Promoción añadida correctamente.');
    }

    public function updatePromocion(Request $request, $id)
    {
        $promocion = Promocion::findOrFail($id);
        
        $request->validate([
            'titulo' => 'required|string',
            'precio_especial' => 'required|numeric',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after_or_equal:fecha_inicio',
        ]);

        $promocion->update([
            'titulo' => $request->titulo,
            'descripcion_detallada' => $request->descripcion_detallada,
            'precio_especial' => $request->precio_especial,
            'fecha_inicio' => $request->fecha_inicio,
            'fecha_fin' => $request->fecha_fin,
        ]);

        return redirect()->route('admin.config.promociones')->with('success', 'Promoción actualizada correctamente.');
    }

    public function destroyPromocion($id)
    {
        $promocion = Promocion::findOrFail($id);
        $promocion->delete();
        return redirect()->route('admin.config.promociones')->with('success', 'Promoción eliminada correctamente.');
    }

    public function togglePromocion($id)
    {
        $promocion = Promocion::findOrFail($id);
        $promocion->esta_activa = !$promocion->esta_activa;
        $promocion->save();
        return redirect()->back()->with('success', 'Estado de promoción actualizado.');
    }
}
