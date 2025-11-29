<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SuspencionDireccionInspeccionVehicular extends Model
{
    protected $table = 'suspencion_direccion_inspeccion_vehicular';
    protected $fillable = [
        'amortiguadores_suspencion',
        'juntas_direccion_rotulas',
        'notas'
    ];
    protected $cast=[
    ];
    public function amortiguadores_suspencion_estatus(){
        return $this->hasMany(Estatus::class, 'amortiguadores_suspencion');
    }
    public function juntas_direccion_rotulas_estatus(){
        return $this->hasMany(Estatus::class, 'juntas_direccion_rotulas');
    }
}
