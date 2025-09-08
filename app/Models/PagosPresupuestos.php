<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class PagosPresupuestos extends Model
{
     use SoftDeletes;

    protected $table = 'pagos_presupuestos';

    protected $fillable = [
        'fecha_pagado',
        'importe',
        'presupuestos_id',
    ];

    protected $casts = [
        'fecha_pagado' => 'datetime',
        'importe' => 'decimal:2',
    ];

    // Relaciones
    public function presupuesto()
    {
        return $this->belongsTo(Presupuestos::class, 'presupuestos_id');
    }

}
