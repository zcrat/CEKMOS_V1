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

    public function OrdenesServicios()
    {
        return $this->hasMany(OrdenesServicio::class, 'tipo_vehiculo_concepto_id');
    }
    public function ModulosOrden()
    {
        return $this->hasMany(VehiculosConceptosDisponibles::class, 'vehiculo_concepto_id');
    }
}
