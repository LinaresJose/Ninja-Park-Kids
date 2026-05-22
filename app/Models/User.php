<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    // 1. LE DECIMOS QUE USE TU TABLA EN ESPAÑOL
    protected $table = 'usuarios';

    public $timestamps = false;

    // 2. CAMPOS QUE COINCIDEN CON TU SQL
    protected $fillable = [
        'cedula',
        'nombre',
        'apellido',
        'correo',
        'password',
        'rol_id',
        'estado',
    ];

    // 3. OCULTAR DATOS SENSIBLES
    protected $hidden = [
        'password',
    ];

    // 4. RELACIÓN CON EL ROL
    public function rol()
    {
        return $this->belongsTo(Rol::class, 'rol_id');
    }

    // 5. MAPEAR EL EMAIL (Laravel busca 'email' por defecto, tú usas 'correo')
    public function getEmailAttribute()
    {
        return $this->correo;
    }

    // 6. Helpers para roles
    public function esGerente() { return $this->rol->nombre_rol === 'Gerente'; }
    public function esAdmin()   { return $this->rol->nombre_rol === 'Admin'; }
    public function esOperador(){ return $this->rol->nombre_rol === 'Operador Integral'; }
    
    // Nivel jerárquico: El Gerente puede todo lo de Admin, Admin puede todo lo de Operador
    public function tieneAccesoAdmin() { return in_array($this->rol->nombre_rol, ['Gerente', 'Admin']); }
    public function tieneAccesoOperador() { return in_array($this->rol->nombre_rol, ['Gerente', 'Admin', 'Operador Integral']); }
}