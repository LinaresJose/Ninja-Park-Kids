<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Rol;

class RolesSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            ['nombre_rol' => 'Gerente'],
            ['nombre_rol' => 'Admin'],
            ['nombre_rol' => 'Operador Integral'],
        ];

        foreach ($roles as $rol) {
            Rol::updateOrCreate(['nombre_rol' => $rol['nombre_rol']], $rol);
        }
    }
}
