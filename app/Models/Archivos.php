<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Archivos extends Model
{
    use SoftDeletes;
    protected $table = 'archivos';

    protected $fillable = [
        'nombre',
        'orden_servicio_id',
        'presupuesto_id',
        'tipo_id',
    ];
    public function orden_servicio(){
        return $this->belongsTo(OrdenesServicio::class, 'orden_servicio_id');
    }
    public function presupuesto(){
        return $this->belongsTo(Presupuestos::class, 'presupuesto_id');
    }
    public function tipo(){
        return $this->belongsTo(Tipos::class, 'tipo_id');
    }

}
