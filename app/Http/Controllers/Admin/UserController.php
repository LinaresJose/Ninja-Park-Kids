<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use App\Models\Rol;

class UserController extends Controller
{
    public function index()
    {
        // Solo Gerente puede gestionar usuarios
        if (!Auth::user()->esGerente()) {
            return redirect()->route('admin.dashboard')->with('error', 'No tiene permisos para gestionar usuarios.');
        }

        $usuarios = User::with('rol')->get();
        $roles = Rol::orderBy('id')->get();
        return view('admin.users', compact('usuarios', 'roles'));
    }
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

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'nombre'   => 'required|string|max:255',
            'apellido' => 'required|string|max:255',
            'cedula'   => 'required|string|unique:usuarios,cedula,' . $id,
            'correo'   => 'required|email|max:255|unique:usuarios,correo,' . $id,
            'rol_id'   => 'required|exists:roles,id',
            'password' => 'nullable|string|min:4',
        ], [
            'cedula.unique' => 'Esa cédula ya está registrada por otro usuario.',
            'correo.unique' => 'Ese correo ya está en uso por otro usuario.',
        ]);

        if ($validator->fails()) {
            return redirect()->route('admin.users')
                        ->withErrors($validator)
                        ->withInput();
        }

        $data = [
            'nombre'   => $request->nombre,
            'apellido' => $request->apellido,
            'cedula'   => $request->cedula,
            'correo'   => $request->correo,
            'rol_id'   => $request->rol_id,
        ];

        // Solo actualizar contraseña si se envió una nueva
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect()->route('admin.users')->with('success', 'Usuario actualizado exitosamente.');
    }
}
