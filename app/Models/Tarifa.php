<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tarifa extends Model
{
    protected $fillable = [
        'nombre_tarifa',
        'duracion_minutos',
        'precio',
        'esta_activa'
    ];

    protected $casts = [
        'precio' => 'decimal:2',
        'esta_activa' => 'boolean',
    ];
}
