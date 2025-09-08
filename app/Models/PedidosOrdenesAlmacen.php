<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PedidosOrdenesAlmacen extends Model
{
   protected $table = 'pedidos_ordenes_almacens';

    protected $fillable = [
        'pedido_hecho',
        'pedido_entregado',
        'orden_servicio_id',
    ];

    protected $casts = [
        'pedido_hecho' => 'datetime',
        'pedido_entregado' => 'datetime',
    ];

    // Relaciones
    public function orden_servicio()
    {
        return $this->belongsTo(OrdenesServicio::class,'orden_servicio_id');
    }
}
