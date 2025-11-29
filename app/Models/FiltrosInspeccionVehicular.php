<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FiltrosInspeccionVehicular extends Model
{
    protected $table = 'filtros_inspeccion_vehicular';
    protected $fillable = [
        'aire',
        'combustible',
        'aceite',
        'notas'
    ];
    protected $cast=[
    ];
    public function aire_estatus(){
        return $this->hasMany(Estatus::class, 'aire');
    }
    public function combustible_estatus(){
        return $this->hasMany(Estatus::class, 'combustible');
    }
    public function aceite_estatus(){
        return $this->hasMany(Estatus::class, 'aceite');
    }
}
