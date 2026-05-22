<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TerminoCondicion;

class LegalController extends Controller
{
    public function index()
    {
        $versiones = TerminoCondicion::orderBy('id', 'desc')->get();
        return view('admin.legal.index', compact('versiones'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'version' => 'required|string|max:255',
            'contenido' => 'required|string',
        ]);

        $isActivo = $request->has('activo') ? true : false;

        if ($isActivo) {
            TerminoCondicion::query()->update(['activo' => false]);
        }

        TerminoCondicion::create([
            'version' => $request->version,
            'contenido' => $request->contenido,
            'activo' => $isActivo,
        ]);

        return redirect()->route('admin.legal.index')->with('success', 'Nueva versión legal guardada.');
    }

    public function update(Request $request, $id)
    {
        $termino = TerminoCondicion::findOrFail($id);

        $request->validate([
            'version' => 'required|string|max:255',
            'contenido' => 'required|string',
        ]);

        $termino->update([
            'version' => $request->version,
            'contenido' => $request->contenido,
        ]);

        return redirect()->route('admin.legal.index')->with('success', 'Versión legal actualizada.');
    }

    public function toggleActivo($id)
    {
        TerminoCondicion::where('id', '!=', $id)->update(['activo' => false]);
        TerminoCondicion::where('id', $id)->update(['activo' => true]);
        
        return redirect()->back()->with('success', 'Versión marcada como activa.');
    }
}
