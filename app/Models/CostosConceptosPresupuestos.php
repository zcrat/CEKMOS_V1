<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CostosConceptosPresupuestos extends Model
{
    protected $table = 'costos_conceptos_presupuestos';

    protected $fillable = [
        'concepto_presupuesto_id',
        'vehiculo_concepto_id',
        'usuario_id',
        'p_refaccion',
        'p_mano_obra',
        'p_total',
    ];

    protected $casts = [
        'p_refaccion' => 'decimal:2',
        'p_mano_obra' => 'decimal:2',
        'p_total' => 'decimal:2',
    ];

    public function concepto_presupuesto()
    {
        return $this->belongsTo(ConceptosPresupuestos::class, 'concepto_presupuesto_id');
    }

    public function vehiculo_concepto()
    {
        return $this->belongsTo(VehiculosConceptos::class, 'vehiculo_concepto_id');
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }
}
