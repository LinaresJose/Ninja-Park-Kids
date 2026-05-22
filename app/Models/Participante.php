<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Participante extends Model
{
    public $timestamps = false;
    protected $fillable = ['representante_id', 'nombre', 'apellido', 'fecha_nacimiento'];

    // Accessor: Nombre completo
    public function getNombreCompletoAttribute(): string
    {
        return trim($this->nombre . ' ' . $this->apellido);
    }

    // Accessor: Edad calculada desde fecha_nacimiento
    public function getEdadAttribute(): ?int
    {
        if (!$this->fecha_nacimiento) {
            return null;
        }
        return Carbon::parse($this->fecha_nacimiento)->age;
    }

    // Relación: Muchos niños pertenecen a un Representante
    public function representante()
    {
        return $this->belongsTo(Representante::class);
    }
}