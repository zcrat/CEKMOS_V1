<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrdenesServicio extends Model
{
    protected $table = 'ordenes_servicios';
    protected $fillable = [
        'orden_servicio',
        'orden_seguimiento',
        'modulo_orden_id',
        'vehiculo_id',
        'tipo_vehiculo_concepto_id',
        'user_id',
        'empresa_id',
        'update_fotos',
        'diagnostico',
        'indicaciones_cliente',
        'notas_mecanico',
        'notas_retraso',
        'telefono',
        'ubicacion',
    ];

}
