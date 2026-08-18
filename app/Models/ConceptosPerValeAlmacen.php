<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConceptosPerValeAlmacen extends Model
{
    protected $table = 'conceptos_per_vale_almacen';

    protected $fillable = [
        'vale_almacen_id',
        'concepto_id',
        'cantidad',
        'fecha_entrega',
    ];

    protected $casts = [
        'cantidad' => 'decimal:2',
        'fecha_entrega' => 'datetime',
    ];

    public function concepto()
    {
        return $this->belongsTo(ConceptosAlmacen::class, 'concepto_id');
    }

    public function vale_almacen()
    {
        return $this->belongsTo(ValesAlmacen::class, 'vale_almacen_id');
    }
}
