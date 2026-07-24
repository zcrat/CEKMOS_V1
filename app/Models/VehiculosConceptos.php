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
        'años',
        'modelo_id',
    ];

    protected $casts = [
        'años' => 'array',
    ];

    public function modelo()
    {
        return $this->belongsTo(Modelos::class, 'modelo_id');
    }

    public function conceptos_presupuestos()
    {
        return $this->belongsToMany(
            ConceptosPresupuestos::class,
            'costos_conceptos_presupuestos',
            'vehiculo_concepto_id',
            'concepto_presupuesto_id'
        )->withPivot(['p_refaccion', 'p_mano_obra', 'p_total'])
            ->withTimestamps();
    }

    public function costos_conceptos_presupuestos()
    {
        return $this->hasMany(CostosConceptosPresupuestos::class, 'vehiculo_concepto_id');
    }

    public function ordenes_servicio()
    {
        return $this->hasMany(OrdenesServicio::class, 'vehiculo_concepto_id');
    }

    public function modulos_orden()
    {
        return $this->hasMany(VehiculosConceptosDisponibles::class, 'vehiculo_concepto_id');
    }
}
