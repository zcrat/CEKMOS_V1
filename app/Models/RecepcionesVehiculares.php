<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RecepcionesVehiculares extends Model
{
    protected $table = 'recepciones_vehiculares';

    protected $fillable = [
        'orden_servicio_id',
        'is_ficticia',
        'cambiar_archivos',
        'indicaciones_cliente',
    ];

    protected $casts = [
        'is_ficticia' => 'boolean',
        'cambiar_archivos' => 'boolean',
    ];

    public function orden_servicio()
    {
        return $this->belongsTo(OrdenesServicio::class, 'orden_servicio_id');
    }

    public function interiores()
    {
        return $this->hasOne(InterioresOrdenServicio::class, 'recepcion_vehicular_id');
    }

    public function exteriores()
    {
        return $this->hasOne(ExterioresOrdenServicio::class, 'recepcion_vehicular_id');
    }

    public function inventario()
    {
        return $this->hasOne(InventarioOrdenServicio::class, 'recepcion_vehicular_id');
    }

    public function condiciones_pintura()
    {
        return $this->hasOne(CondicionesPinturaOrdenServicio::class, 'recepcion_vehicular_id');
    }

    public function archivos()
    {
        return $this->hasMany(Archivos::class, 'recepcion_vehicular_id');
    }
}
