<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BandasInspeccionVehicular extends Model
{
     protected $table = 'bandas_inspeccion_vehicular';
    protected $fillable = [
        'accesorios',
        'bandas_direccion_hidraulica',
        'alternador_aire_acondicionado',
    ];
    public function accesorios_estatus(){
        return $this->hasMany(Estatus::class, 'accesorios');
    }
    public function bandas_direccion_hidraulica_estatus(){
        return $this->hasMany(Estatus::class, 'bandas_direccion_hidraulica');
    }
    public function lternador_aire_acondicionado_estatus(){
        return $this->hasMany(Estatus::class, 'alternador_aire_acondicionado');
    }

}
