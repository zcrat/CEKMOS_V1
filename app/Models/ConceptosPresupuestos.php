<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConceptosPresupuestos extends Model
{
    protected $table = 'conceptos_presupuestos';
    protected $fillable = [
        'descripcion',
        'num',
        'p_refaccion',
        'p_mano_obra',
        'p_total',
        'tipo_id',
        'vehiculo_concepto_id',
        'categoria_sat_id',
        'unidad_sat_id',
        'modulo_orden_servicio_id',
    ];
    protected $cast=[
        'p_refaccion'=>'decimal:2',
        'p_total'=>'decimal:2',
        'p_mano_obra'=>'decimal:2',
    ];

    public function tipo(){
        return $this->belongsTo(Tipos::class,'tipo_id');
    }
    public function vehiculo_concepto(){
        return $this->belongsTo(VehiculosConceptos::class,'vehiculo_concepto_id');
    } 
    public function categoria_sat(){
        return $this->belongsTo(CategoriasSat::class,'categoria_sat_id');
    }
    public function unidad_sat(){
        return $this->belongsTo(UnidadesSat::class,'unidad_sat_id');
    }
    public function modulo_orden_servicio(){
        return $this->belongsTo(ModuloOrdenesServicio::class,'modulo_orden_servicio_id');
    }
}
