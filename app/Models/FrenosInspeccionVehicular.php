<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FrenosInspeccionVehicular extends Model
{
    protected $table = 'frenos_inspeccion_vehicular';
    protected $fillable = [
        'pastillas_izquierda_delantera',
        'pastillas_izquierda_trasera',
        'pastillas_derecha_delantera',
        'pastillas_derecha_trasera',
        'rotores_izquierda_delantera',
        'rotores_izquierda_trasera',
        'rotores_derecha_delantera	',
        'rotores_derecha_trasera',
        'pinzas_cilindros_rueda_izquierda_delantera',
        'pinzas_cilindros_rueda_izquierda_trasera',
        'pinzas_cilindros_rueda_derecha_delantera',
        'pinzas_cilindros_rueda_derecha_trasera',
    ];
    protected $cast=[
    ];
    public function pastillas_izquierda_delantera_estatus(){
        return $this->hasMany(Estatus::class, 'pastillas_izquierda_delantera');
    }
    public function pastillas_izquierda_trasera_estatus(){
        return $this->hasMany(Estatus::class, 'pastillas_izquierda_trasera');
    }
    public function pastillas_derecha_delantera_estatus(){
        return $this->hasMany(Estatus::class, 'pastillas_derecha_delantera');
    }
    public function pastillas_derecha_trasera_estatus(){
        return $this->hasMany(Estatus::class, 'pastillas_derecha_trasera');
    }
    public function rotores_izquierda_delantera_estatus(){
        return $this->hasMany(Estatus::class, 'rotores_izquierda_delantera');
    }
    public function rotores_izquierda_trasera_estatus(){
        return $this->hasMany(Estatus::class, 'rotores_izquierda_trasera');
    }
    public function rotores_derecha_delantera_estatus(){
        return $this->hasMany(Estatus::class, 'rotores_derecha_delantera');
    }
    public function rotores_derecha_trasera_estatus(){
        return $this->hasMany(Estatus::class, 'rotores_derecha_trasera');
    }
    public function pinzas_cilindros_rueda_izquierda_delantera_estatus(){
        return $this->hasMany(Estatus::class, 'pinzas_cilindros_rueda_izquierda_delantera');
    }
    public function pinzas_cilindros_rueda_izquierda_trasera_estatus(){
        return $this->hasMany(Estatus::class, 'pinzas_cilindros_rueda_izquierda_trasera');
    }
    public function pinzas_cilindros_rueda_derecha_delantera_estatus(){
        return $this->hasMany(Estatus::class, 'pinzas_cilindros_rueda_derecha_delantera');
    }
    public function pinzas_cilindros_rueda_derecha_trasera_estatus(){
        return $this->hasMany(Estatus::class, 'pinzas_cilindros_rueda_derecha_trasera');
    }
}
