<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ElectricoInspeccionVehicular extends Model
{
    protected $table = 'electrico_inspeccion_vehicular';
    protected $fillable = [
        'sistema_carga_bateria',
        'cables_conexiones_fusibles',
        'faro_izquierda',
        'faro_derecha',
        'cuarto_izquierda',
        'cuarto_derecha',
        'reversa_frenos',
        'direccionales_izquierda_delantera',
        'direccionales_derecha_delantera',
        'direccionales_izquierda_trasera',
        'direccionales_derecha_trasera',
        'intermitentes',
    ];
    public function sistema_carga_bateria_estatus(){
        return $this->hasMany(Estatus::class, 'sistema_carga_bateria');
    }
    public function cables_conexiones_fusibles_estatus(){
        return $this->hasMany(Estatus::class, 'cables_conexiones_fusibles');
    }
    public function faro_izquierda_estatus(){
        return $this->hasMany(Estatus::class, 'faro_izquierda');
    }
    public function faro_derecha_estatus(){
        return $this->hasMany(Estatus::class, 'faro_derecha');
    }
    public function cuarto_izquierda_estatus(){
        return $this->hasMany(Estatus::class, 'cuarto_izquierda');
    }
    public function cuarto_derecha_estatus(){
        return $this->hasMany(Estatus::class, 'cuarto_derecha');
    }
    public function reversa_frenos_estatus(){
        return $this->hasMany(Estatus::class, 'reversa_frenos');
    }
    public function direccionales_izquierda_delantera_estatus(){
        return $this->hasMany(Estatus::class, 'direccionales_izquierda_delantera');
    }
    public function direccionales_derecha_delantera_estatus(){
        return $this->hasMany(Estatus::class, 'direccionales_derecha_delantera');
    }
    public function direccionales_izquierda_trasera_estatus(){
        return $this->hasMany(Estatus::class, 'direccionales_izquierda_trasera');
    }
    public function direccionales_derecha_trasera_estatus(){
        return $this->hasMany(Estatus::class, 'direccionales_derecha_trasera');
    }
    public function intermitentes_estatus(){
        return $this->hasMany(Estatus::class, 'intermitentes');
    }
}
