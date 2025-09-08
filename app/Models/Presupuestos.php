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
    public function OrdenServicio()
    {
        return $this->belongsTo(OrdenesServicio::class,'orden_servicio_id');
    }

    public function Factura()
    {
        return $this->belongsTo(Facturas::class,'factura_id');
    }

    public function Tipo()
    {
        return $this->belongsTo(Tipos::class,'tipo_id');
    }

    public function Estatus()
    {
        return $this->belongsTo(Estatus::class,'estatus_id');
    }

    public function Pago()
    {
        return $this->hasOne(PagosPresupuestos::class,'presupuestos_id');
    }

}
