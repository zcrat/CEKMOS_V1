<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ManguerasInspeccionVehicular extends Model
{
    protected $table = 'mangueras_inspeccion_vehicular';
    protected $fillable = [
        'refrigerante',
        'direccion_aire_acondicionado',
        'calefaccion',
    ];
    protected $cast=[
    ];
    public function refrigerante_estatus(){
        return $this->hasMany(Estatus::class, 'refrigerante');
    }
    public function direccion_aire_acondicionado_estatus(){
        return $this->hasMany(Estatus::class, 'direccion_aire_acondicionado');
    }
    public function calefaccion_estatus(){
        return $this->hasMany(Estatus::class, 'calefaccion');
    }
}
