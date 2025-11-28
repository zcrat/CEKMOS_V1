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
        'revision_luces_espias_id',
        'mangueras_id',
        'user_id',
    ];

}
