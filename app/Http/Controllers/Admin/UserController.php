<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|max:255',
            'apellido' => 'required|string|max:255',
            'cedula' => 'required|string|unique:usuarios,cedula',
            'correo' => 'required|string|email|max:255|unique:usuarios,correo',
            'password' => 'required|string|min:4',
            'rol_id' => 'required|exists:roles,id',
        ]);

        if ($validator->fails()) {
            return redirect()->route('admin.users')
                        ->withErrors($validator)
                        ->withInput();
        }

        User::create([
            'nombre' => $request->nombre,
            'apellido' => $request->apellido,
            'cedula' => $request->cedula,
            'correo' => $request->correo,
            'password' => Hash::make($request->password),
            'rol_id' => $request->rol_id,
            'estado' => true,
        ]);

        return redirect()->route('admin.users')->with('success', 'Usuario creado exitosamente.');
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        
        // Prevent self-deactivation
        if (auth()->id() === $user->id) {
            return redirect()->route('admin.users')->with('error', 'No puedes desactivar o activar tu propia cuenta.');
        }

        // Alternar el estado
        $nuevoEstado = !$user->estado;
        $user->update(['estado' => $nuevoEstado]);

        $mensaje = $nuevoEstado ? 'Usuario activado exitosamente.' : 'Usuario desactivado exitosamente.';
        return redirect()->route('admin.users')->with('success', $mensaje);
    }
}
