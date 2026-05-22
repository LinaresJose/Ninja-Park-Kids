<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Promocion extends Model
{
    protected $table = 'promociones';

    protected $fillable = [
        'titulo',
        'descripcion_detallada',
        'precio_especial',
        'fecha_inicio',
        'fecha_fin',
        'esta_activa'
    ];

    protected $casts = [
        'precio_especial' => 'decimal:2',
        'fecha_inicio' => 'date',
        'fecha_fin' => 'date',
        'esta_activa' => 'boolean',
    ];
}
