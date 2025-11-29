<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TrenTransmisionInspeccionVehicular extends Model
{
    protected $table = 'tren_transmision_inspeccion_vehicular';
    protected $fillable = [
        'filtro_transmison',
        'union_transmision_clutch',
        'eje_traccion_juntas_homocineticas',
        'eje_transmision_juntas_universales',
        'rodamientos_rueda',
        'tren_transmision',
        'clutch',
        'notas'
    ];
    protected $cast=[
    ];
    public function filtro_transmison_estatus(){
        return $this->hasMany(Estatus::class, 'filtro_transmison');
    }
    public function union_transmision_clutch_estatus(){
        return $this->hasMany(Estatus::class, 'union_transmision_clutch');
    }
    public function eje_traccion_juntas_homocineticas_estatus(){
        return $this->hasMany(Estatus::class, 'eje_traccion_juntas_homocineticas');
    }
    public function eje_transmision_juntas_universales_estatus(){
        return $this->hasMany(Estatus::class, 'eje_transmision_juntas_universales');
    }
    public function rodamientos_rueda_estatus(){
        return $this->hasMany(Estatus::class, 'rodamientos_rueda');
    }
    public function tren_transmision_estatus(){
        return $this->hasMany(Estatus::class, 'tren_transmision');
    }
    public function clutch_estatus(){
        return $this->hasMany(Estatus::class, 'clutch');
    }
}
