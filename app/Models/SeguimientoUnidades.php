<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SeguimientoUnidades extends Model
{
    protected $fillable = [
        'orden_servicio_id',
        'tipo_id',
    ];
    protected $table='seguimiento_unidades';

    public function ordenen_servicio(){
        return $this->belongsTo(OrdenesServicio::class, 'orden_servicio_id');
    }
}
