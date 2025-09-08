<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class VehiculosConceptosDisponibles extends Model
{
    use SoftDeletes;
    protected $table = 'vehiculos_conceptos_disponibles';
    protected $fillable = [
        'vehiculo_concepto_id',
        'modulo_orden_id',
    ];

    // Relaciones
    public function VehiculoConcepto()
    {
        return $this->belongsTo(VehiculosConceptos::class, 'vehiculo_concepto_id');
    }

    public function ModuloOrden()
    {
        return $this->belongsTo(ModuloOrdenesServicio::class, 'modulo_orden_id');
    }

}
