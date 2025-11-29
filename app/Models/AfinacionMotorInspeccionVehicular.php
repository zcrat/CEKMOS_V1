<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AfinacionMotorInspeccionVehicular extends Model
{
    protected $table = 'afinacion_motor_inspeccion_vehicular';
    protected $fillable = [
        'tapa_distribuidor_bujias_cables',
        'fuel_injection',
    ];
    protected $cast=[
    ];
    public function tapa_distribuidor_bujias_cables_estatus(){
        return $this->hasMany(Estatus::class, 'tapa_distribuidor_bujias_cables');
    }
    public function fuel_injection_estatus(){
        return $this->hasMany(Estatus::class, 'fuel_injection');
    }
    
}
