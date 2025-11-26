<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LogAccionesOS extends Model
{
    protected $table='log_acciones';
    protected $fillable = [
        'que_hizo',
        'orden_servicio_id',
        'presupuesto_id',
        'tipo_id',
    ];

    
    public function orden_servicio()
    {
        return $this->belongsTo(OrdenesServicio::class,'orden_servicio_id');
    }

    public function presupuesto()
    {
        return $this->belongsTo(Presupuestos::class,'presupuesto_id');
    }

    public function tipo()
    {
        return $this->belongsTo(Tipos::class, 'tipo_id');
    }

}
