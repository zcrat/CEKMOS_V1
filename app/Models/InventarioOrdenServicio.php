<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
class InventarioOrdenServicio extends Model
{
    protected $table = 'inventario_orden_servicios';
    public $timestamps=false;
    protected $fillable = [
        'recepcion_vehicular_id',
        'llanta',
        'cubreruedas',
        'cables_corriente',
        'candado_ruedas',
        'estuche_herramientas',
        'gato',
        'llave_tuercas',
        'trajeta_circulacion',
        'triangulo_seguridad',
        'extinguidor',
        'placas',
    ];

    protected $casts = [
        'llanta' => 'boolean',
        'cubreruedas' => 'boolean',
        'cables_corriente' => 'boolean',
        'candado_ruedas' => 'boolean',
        'estuche_herramientas' => 'boolean',
        'gato' => 'boolean',
        'llave_tuercas' => 'boolean',
        'trajeta_circulacion' => 'boolean',
        'triangulo_seguridad' => 'boolean',
        'extinguidor' => 'boolean',
        'placas' => 'boolean',
    ];

    // Relación principal
    public function recepcion_vehicular()
    {
        return $this->belongsTo(RecepcionesVehiculares::class,'recepcion_vehicular_id');
    }

}
