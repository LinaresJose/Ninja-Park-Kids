<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HorarioParque extends Model
{
    protected $table = 'horarios_parque';

    protected $fillable = [
        'dia_semana',
        'hora_apertura',
        'hora_cierre',
        'esta_cerrado'
    ];

    protected $casts = [
        'esta_cerrado' => 'boolean',
    ];
}
