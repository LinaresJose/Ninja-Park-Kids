<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Promocion;
use Illuminate\Http\Request;

class PromocionController extends Controller
{
    /**
     * Muestra el listado de promociones ordenadas por fecha de fin.
     */
    public function index()
    {
        $promociones = Promocion::orderBy('fecha_fin', 'desc')->get();
        return view('admin.config.promociones', compact('promociones'));
    }

    /**
     * Guarda una nueva promoción.
     */
    public function store(Request $request)
    {
        $request->validate([
            'titulo'                => 'required|string|max:150',
            'descripcion_detallada' => 'nullable|string',
            'precio_especial'       => 'required|numeric|min:0',
            'fecha_inicio'          => 'required|date',
            'fecha_fin'             => 'required|date|after_or_equal:fecha_inicio',
        ]);

        Promocion::create([
            'titulo'                => $request->titulo,
            'descripcion_detallada' => $request->descripcion_detallada,
            'precio_especial'       => $request->precio_especial,
            'fecha_inicio'          => $request->fecha_inicio,
            'fecha_fin'             => $request->fecha_fin,
            'esta_activa'           => $request->boolean('esta_activa'),
        ]);

        return redirect()->route('admin.config.promociones')->with('success', 'Promoción añadida correctamente.');
    }

    /**
     * Actualiza una promoción existente.
     */
    public function update(Request $request, $id)
    {
        $promocion = Promocion::findOrFail($id);

        $request->validate([
            'titulo'          => 'required|string|max:150',
            'precio_especial' => 'required|numeric|min:0',
            'fecha_inicio'    => 'required|date',
            'fecha_fin'       => 'required|date|after_or_equal:fecha_inicio',
        ]);

        $promocion->update($request->only([
            'titulo', 'descripcion_detallada', 'precio_especial', 'fecha_inicio', 'fecha_fin',
        ]));

        return redirect()->route('admin.config.promociones')->with('success', 'Promoción actualizada correctamente.');
    }

    /**
     * Elimina una promoción.
     */
    public function destroy($id)
    {
        Promocion::findOrFail($id)->delete();
        return redirect()->route('admin.config.promociones')->with('success', 'Promoción eliminada correctamente.');
    }

    /**
     * Activa/desactiva una promoción.
     */
    public function toggle($id)
    {
        $promocion = Promocion::findOrFail($id);
        $promocion->update(['esta_activa' => !$promocion->esta_activa]);
        return redirect()->back()->with('success', 'Estado de promoción actualizado.');
    }
}
