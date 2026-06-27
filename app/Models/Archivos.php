<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Archivos extends Model
{
    use SoftDeletes;
    protected $table = 'archivos';

    protected $fillable = [
        'nombre',
        'recepcion_vehicular_id',
        'presupuesto_id',
        'tipo_id',
        'estatus_id',
    ];
    public function recepcion_vehicular(){
        return $this->belongsTo(RecepcionesVehiculares::class, 'recepcion_vehicular_id');
    }
    public function presupuesto(){
        return $this->belongsTo(Presupuestos::class, 'presupuesto_id');
    }
    public function tipo(){
        return $this->belongsTo(Tipos::class, 'tipo_id');
    }
    public function estatus(){
        return $this->belongsTo(Tipos::class, 'tipo_id');
    }

}
