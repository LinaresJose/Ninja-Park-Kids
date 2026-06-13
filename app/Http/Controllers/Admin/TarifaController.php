<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tarifa;
use App\Models\HorarioParque;
use Illuminate\Http\Request;

class TarifaController extends Controller
{
    /**
     * Muestra la lista de tarifas y los horarios del parque.
     */
    public function index()
    {
        $tarifas  = Tarifa::all();
        $horarios = HorarioParque::orderBy('dia_semana')->get();

        // Si no hay horarios, sembrar los 7 días de la semana de una vez (insert masivo)
        if ($horarios->isEmpty()) {
            $semana = [];
            for ($i = 0; $i <= 6; $i++) {
                $semana[] = ['dia_semana' => $i];
            }
            HorarioParque::insert($semana); // Una sola query en lugar de 7
            $horarios = HorarioParque::orderBy('dia_semana')->get();
        }

        return view('admin.config.tarifas', compact('tarifas', 'horarios'));
    }

    /**
     * Guarda una nueva tarifa.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nombre_tarifa'    => 'required|string|max:100',
            'duracion_minutos' => 'nullable|integer|min:1',
            'precio'           => 'required|numeric|min:0',
        ]);

        Tarifa::create([
            'nombre_tarifa'    => $request->nombre_tarifa,
            'duracion_minutos' => $request->duracion_minutos,
            'precio'           => $request->precio,
            'esta_activa'      => $request->boolean('esta_activa'),
        ]);

        return redirect()->route('admin.config.tarifas')->with('success', 'Tarifa añadida correctamente.');
    }

    /**
     * Actualiza una tarifa existente.
     */
    public function update(Request $request, $id)
    {
        $tarifa = Tarifa::findOrFail($id);

        $request->validate([
            'nombre_tarifa'    => 'required|string|max:100',
            'duracion_minutos' => 'nullable|integer|min:1',
            'precio'           => 'required|numeric|min:0',
        ]);

        $tarifa->update($request->only(['nombre_tarifa', 'duracion_minutos', 'precio']));

        return redirect()->route('admin.config.tarifas')->with('success', 'Tarifa actualizada correctamente.');
    }

    /**
     * Elimina una tarifa.
     */
    public function destroy($id)
    {
        Tarifa::findOrFail($id)->delete();
        return redirect()->route('admin.config.tarifas')->with('success', 'Tarifa eliminada correctamente.');
    }

    /**
     * Activa/desactiva una tarifa.
     */
    public function toggle($id)
    {
        $tarifa = Tarifa::findOrFail($id);
        $tarifa->update(['esta_activa' => !$tarifa->esta_activa]);
        return redirect()->back()->with('success', 'Estado de tarifa actualizado.');
    }

    /**
     * Actualiza masivamente los horarios operativos del parque.
     */
    public function updateHorarios(Request $request)
    {
        $request->validate([
            'horarios'                 => 'required|array',
            'horarios.*.hora_apertura' => 'nullable|date_format:H:i',
            'horarios.*.hora_cierre'   => 'nullable|date_format:H:i',
        ], [
            'horarios.required'                     => 'Debe enviar los horarios del parque.',
            'horarios.array'                        => 'Los horarios enviados tienen un formato incorrecto.',
            'horarios.*.hora_apertura.date_format' => 'La hora de apertura debe tener el formato HH:MM.',
            'horarios.*.hora_cierre.date_format'   => 'La hora de cierre debe tener el formato HH:MM.',
        ]);

        foreach ($request->input('horarios', []) as $id => $data) {
            HorarioParque::where('id', $id)->update([
                'hora_apertura' => $data['hora_apertura'] ?? null,
                'hora_cierre'   => $data['hora_cierre'] ?? null,
                'esta_cerrado'  => isset($data['esta_cerrado']),
            ]);
        }

        return redirect()->route('admin.config.tarifas')->with('success', 'Horarios actualizados correctamente.');
    }
}
