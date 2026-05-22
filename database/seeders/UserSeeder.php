<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Rol;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $rolGerente = Rol::where('nombre_rol', 'Gerente')->first();

        User::updateOrCreate(
            ['correo' => '0.joselinares@gmail.com'],
            [
                'nombre'    => 'Jose',
                'apellido'  => 'Linares',
                'cedula'    => '12345678', // Dato requerido por el esquema
                'password'  => Hash::make('123'),
                'rol_id'    => $rolGerente->id,
                'estado'    => true,
            ]
        );
    }
}
