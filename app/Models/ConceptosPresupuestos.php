<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConceptosPresupuestos extends Model
{
    protected $table = 'conceptos_presupuestos';

    protected $fillable = [
        'num',
        'descripcion',
        'garantia_dias',
        'fijo',
        'tipo_id',
        'modulo_orden_servicio_id',
        'categoria_sat_id',
        'unidad_sat_id',
        'archivo_sistema_id',
    ];

    protected $casts = [
        'garantia_dias' => 'integer',
        'fijo' => 'boolean',
    ];

    public function tipo()
    {
        return $this->belongsTo(Tipos::class, 'tipo_id');
    }

    public function costos()
    {
        return $this->hasMany(CostosConceptosPresupuestos::class, 'concepto_presupuesto_id');
    }

    public function vehiculos_conceptos()
    {
        return $this->belongsToMany(
            VehiculosConceptos::class,
            'costos_conceptos_presupuestos',
            'concepto_presupuesto_id',
            'vehiculo_concepto_id'
        )->withPivot(['p_refaccion', 'p_mano_obra', 'p_total'])
            ->withTimestamps();
    }

    public function categoria_sat()
    {
        return $this->belongsTo(CategoriasSat::class, 'categoria_sat_id');
    }

    public function unidad_sat()
    {
        return $this->belongsTo(UnidadesSat::class, 'unidad_sat_id');
    }

    public function modulo_orden_servicio()
    {
        return $this->belongsTo(ModuloOrdenesServicio::class, 'modulo_orden_servicio_id');
    }

    public function archivo_sistema()
    {
        return $this->belongsTo(ArchivoSistema::class, 'archivo_sistema_id');
    }

    public function presupuestos_asignados()
    {
        return $this->hasMany(ConceptosPerPresupuesto::class, 'concepto_presupuesto_id');
    }
}
