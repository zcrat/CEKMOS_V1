<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Presupuestos extends Model
{
     use SoftDeletes;

    protected $table = 'presupuestos';

    protected $fillable = [
        'orden_servicio_id',
        'observaciones',
        'descripcion_mo',
        'garantia',
        'folio',
        'vigencia',
        'factura_id',
        'tipo_id',
        'estatus_id',
        'kilomentraje',
    ];

    // Relaciones
    public function orden_servicio()
    {
        return $this->belongsTo(OrdenesServicio::class,'orden_servicio_id');
    }

    public function factura()
    {
        return $this->belongsTo(Facturas::class,'factura_id');
    }

    public function tipo()
    {
        return $this->belongsTo(Tipos::class,'tipo_id');
    }

    public function estatus()
    {
        return $this->belongsTo(Estatus::class,'estatus_id');
    }

    public function pago()
    {
        return $this->hasOne(PagosPresupuestos::class,'presupuestos_id');
    }

}
