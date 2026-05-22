<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TerminoCondicion extends Model
{
    // Laragon/MySQL usa la tabla terminos_condiciones
    protected $table = 'terminos_condiciones';
    
    public $timestamps = false;

    protected $fillable = [
        'version',
        'contenido',
        'activo'
    ];

    // Relación: Una versión de términos puede estar en muchos acuerdos firmados
    public function acuerdos()
    {
        return $this->hasMany(AcuerdoFirmado::class, 'terminos_id');
    }
}