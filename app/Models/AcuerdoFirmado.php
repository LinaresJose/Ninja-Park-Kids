<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AcuerdoFirmado extends Model
{
    protected $table = 'acuerdos_firmados';
    public $timestamps = false;

    protected $fillable = [
        'representante_id',
        'terminos_id',
        'fecha_firma',
        'token_qr',
        'firma_base64'
    ];

    // Relación: El representante que firmó
    public function representante()
    {
        return $this->belongsTo(Representante::class);
    }

    // Relación: Los términos que se aceptaron en este acuerdo
    public function terminos()
    {
        return $this->belongsTo(TerminoCondicion::class, 'terminos_id');
    }

    // Relación MUCHOS A MUCHOS: Los niños que cubre este acuerdo específico
    public function participantes()
    {
        return $this->belongsToMany(
            Participante::class, 
            'detalle_acuerdo_participantes', 
            'acuerdo_id', 
            'participante_id'
        );
    }
}
