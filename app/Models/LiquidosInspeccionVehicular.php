<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LiquidosInspeccionVehicular extends Model
{
    protected $table = 'liquidos_inspeccion_vehicular';
    protected $fillable = [
        'alternador_aire_acondicionado',
        'alternador_aire_acondicionado_ok',
        'alternador_aire_acondicionado_lleno',
        'transmision',
        'transmision_ok',
        'transmision_lleno',
        'diferencial_frente_trasero',
        'diferencial_frente_trasero_ok',
        'diferencial_frente_trasero_lleno',
        'refrigerante',
        'refrigerante_ok',
        'refrigerante_lleno',
        'frenos',
        'frenos_ok',
        'frenos_lleno',
        'direccion_hidraulica',
        'direccion_hidraulica_ok',
        'direccion_hidraulica_lleno',
        'limpiaparabrisas',
        'limpiaparabrisas_ok',
        'limpiaparabrisas_lleno',
        'notas'
    ];
    protected $cast=[
        'alternador_aire_acondicionado_ok'=>'boolean',
        'alternador_aire_acondicionado_lleno'=>'boolean',
        'transmision_ok'=>'boolean',
        'transmision_lleno'=>'boolean',
        'diferencial_frente_trasero_ok'=>'boolean',
        'diferencial_frente_trasero_lleno'=>'boolean',
        'refrigerante_ok'=>'boolean',
        'refrigerante_lleno'=>'boolean',
        'frenos_ok'=>'boolean',
        'frenos_lleno'=>'boolean',
        'direccion_hidraulica_ok'=>'boolean',
        'direccion_hidraulica_lleno'=>'boolean',
        'limpiaparabrisas_ok'=>'boolean',
        'limpiaparabrisas_lleno'=>'boolean',
    ];
    public function alternador_aire_acondicionado_estatus(){
        return $this->hasMany(Estatus::class, 'alternador_aire_acondicionado');
    }
    public function transmision_estatus(){
        return $this->hasMany(Estatus::class, 'transmision');
    }
    public function diferencial_frente_trasero_estatus(){
        return $this->hasMany(Estatus::class, 'diferencial_frente_trasero');
    }
    public function refrigerante_estatus(){
        return $this->hasMany(Estatus::class, 'refrigerante');
    }
    public function frenos_estatus(){
        return $this->hasMany(Estatus::class, 'frenos');
    }
    public function direccion_hidraulica_estatus(){
        return $this->hasMany(Estatus::class, 'direccion_hidraulica');
    }
    public function limpiaparabrisas_estatus(){
        return $this->hasMany(Estatus::class, 'limpiaparabrisas');
    }
}
