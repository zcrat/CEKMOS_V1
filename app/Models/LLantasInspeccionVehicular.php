<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LLantasInspeccionVehicular extends Model
{
    protected $table = 'llantas_inspeccion_vehicular';
    protected $fillable = [
        'izquierda_delantera',
        'izquierda_delantera_presion',
        'izquierda_trasera',
        'izquierda_trasera_presion',
        'derecha_delantera',
        'derecha_delantera_presion',
        'derecha_trasera',
        'derecha_trasera_presion',
        'refaccion',
        'refaccion_presion',
        'alineacion_balanceo',
    ];
    protected $cast=[
        'izquierda_delantera_presion'=>'decimal:2',
        'izquierda_trasera_presion'=>'decimal:2',
        'derecha_delantera_presion'=>'decimal:2',
        'derecha_trasera_presion'=>'decimal:2',
        'refaccion_presion'=>'decimal:2',
    ];
    public function izquierda_delantera_estatus(){
        return $this->hasMany(Estatus::class, 'izquierda_delantera');
    }
    public function izquierda_trasera_estatus(){
        return $this->hasMany(Estatus::class, 'izquierda_trasera');
    }
    public function derecha_delantera_estatus(){
        return $this->hasMany(Estatus::class, 'derecha_delantera');
    }
    public function derecha_trasera_estatus(){
        return $this->hasMany(Estatus::class, 'derecha_trasera');
    }
    public function refaccion_estatus(){
        return $this->hasMany(Estatus::class, 'refaccion');
    }
    public function alineacion_balanceo_estatus(){
        return $this->hasMany(Estatus::class, 'alineacion_balanceo');
    }
}
