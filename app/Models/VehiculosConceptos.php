<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class VehiculosConceptos extends Model
{
    use SoftDeletes;
    protected $table = 'vehiculos_conceptos';
    protected $fillable = [
        'descripcion',
    ];

    public function conceptos_presupuestos(){
        return $this->hasMany(ConceptosPresupuestos::class, 'vehiculo_concepto_id');
    }
    public function ordenes_servicio(){
        return $this->hasMany(OrdenesServicio::class, 'vehiculo_concepto_id');
    }
    public function modulos_orden(){
        return $this->hasMany(VehiculosConceptosDisponibles::class, 'vehiculo_concepto_id');
    }
}
