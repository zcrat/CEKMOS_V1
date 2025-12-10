<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InspeccionVehicular extends Model
{
    protected $table='inspeccion_vehicular';
    protected $fillable = [
        'orden_servicio_id',
        'llantas_id',
        'liquidos_id',
        'bandas_id',
        'seguridad_id',
        'filtros_id',
        'escape_id',
        'suspencion_direccion_id',
        'afinacion_motor_id',
        'tren_transmision_id',
        'frenos_id',
        'electrico_id',
        'luces_espias_id',
        'mangueras_id',
        'user_id',
    ];

    public function orden_servicio(){
        return $this->belongsTo(OrdenesServicio::class ,'orden_servicio_id');
    }
    public function llantas(){
        return $this->belongsTo(LLantasInspeccionVehicular::class ,'llantas_id');
    }
    public function liquidos(){
        return $this->belongsTo(LiquidosInspeccionVehicular::class ,'liquidos_id');
    }
    public function bandas(){
        return $this->belongsTo(BandasInspeccionVehicular::class ,'bandas_id');
    }
    public function seguridad(){
        return $this->belongsTo(SeguridadInspeccionVehicular::class ,'seguridad_id');
    }
    public function filtros(){
        return $this->belongsTo(FiltrosInspeccionVehicular::class ,'filtros_id');
    }
    public function escape(){
        return $this->belongsTo(EscapeInspeccionVehicular::class ,'escape_id');
    }
    public function suspencion_direccion(){
        return $this->belongsTo(SuspencionDireccionInspeccionVehicular::class ,'suspencion_direccion_id');
    }
    public function afinacion_motor(){
        return $this->belongsTo(AfinacionMotorInspeccionVehicular::class ,'afinacion_motor_id');
    }
    public function tren_transmision(){
        return $this->belongsTo(TrenTransmisionInspeccionVehicular::class ,'tren_transmision_id');
    }
    public function frenos(){
        return $this->belongsTo(FrenosInspeccionVehicular::class ,'frenos_id');
    }
    public function electrico(){
        return $this->belongsTo(ElectricoInspeccionVehicular::class ,'electrico_id');
    }
    public function luces_espias(){
        return $this->belongsTo(LucesEspiasInspeccionVehicular::class ,'luces_espias_id');
    }
    public function mangueras(){
        return $this->belongsTo(ManguerasInspeccionVehicular::class ,'mangueras_id');
    }
    public function user(){
        return $this->belongsTo(User::class ,'user_id');
    }
}
