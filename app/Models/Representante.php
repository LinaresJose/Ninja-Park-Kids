<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Representante extends Model
{
    // Le decimos que no use los campos 'created_at' y 'updated_at' automáticos 
    // porque no los pusimos en el script de MySQL (para simplificar)
    public $timestamps = false;

    // Campos que el sistema puede llenar masivamente
    protected $fillable = [
        'cedula', 
        'nombre', 
        'apellido',
        'fecha_nacimiento',
        'correo', 
        'telefono',
        'parentesco'
    ];

    // Accessor: Nombre completo
    public function getNombreCompletoAttribute(): string
    {
        return trim($this->nombre . ' ' . $this->apellido);
    }

    // Relación: Un representante tiene muchos participantes (niños)
    public function participantes()
    {
        return $this->hasMany(Participante::class);
    }

    // Relación: Un representante tiene muchos acuerdos firmados
    public function acuerdos()
    {
        return $this->hasMany(AcuerdoFirmado::class);
    }
}