<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SeguridadInspeccionVehicular extends Model
{
    protected $table = 'seguridad_inspeccion_vehicular';
    protected $fillable = [
        'frenos_emergencia',
        'limpiaparabrisas_izquierdo_derecho',
        'limpiaparabrisas_trasero',
        'notas'
    ];
    protected $cast=[
    ];
    public function frenos_emergencia_estatus(){
        return $this->hasMany(Estatus::class, 'frenos_emergencia');
    }
    public function limpiaparabrisas_izquierdo_derecho_estatus(){
        return $this->hasMany(Estatus::class, 'limpiaparabrisas_izquierdo_derecho');
    }
    public function limpiaparabrisas_trasero_estatus(){
        return $this->hasMany(Estatus::class, 'limpiaparabrisas_trasero');
    }
}
