<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
class ResponsablesOrdenServicio extends Model
{

    protected $table = 'responsables_orden_servicios';
    public $timestamps=false;
    protected $fillable = [
        'administrador_transporte_id',
        'jefe_de_proceso_id',
        'trabajador_id',
        'tecnico_id',
        'orden_servicio_id',
    ];

    // Relaciones con alias
    public function administrador_transporte(){
        return $this->belongsTo(UsuariosTaller::class, 'administrador_transporte_id');
    }

    public function jefe_de_proceso(){
        return $this->belongsTo(UsuariosTaller::class, 'jefe_de_proceso_id');
    }

    public function trabajador(){
        return $this->belongsTo(UsuariosTaller::class, 'trabajador_id');
    }

    public function tecnico(){
        return $this->belongsTo(UsuariosTaller::class, 'tecnico_id');
    }

    public function orden_servicio(){
        return $this->belongsTo(OrdenesServicio::class,'orden_servicio_id');
    }

}
