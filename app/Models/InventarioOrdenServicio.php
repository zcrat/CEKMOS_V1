<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class InventarioOrdenServicio extends Model
{
    use SoftDeletes;
    protected $table = 'inventario_orden_servicios';

    protected $fillable = [
        'orden_servicio_id',
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
    public function OrdenServicio()
    {
        return $this->belongsTo(OrdenesServicio::class,'orden_servicio_id');
    }

}
