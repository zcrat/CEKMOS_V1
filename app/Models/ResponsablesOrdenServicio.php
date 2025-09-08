<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class ResponsablesOrdenServicio extends Model
{
    use SoftDeletes;
    protected $table = 'responsables_orden_servicios';

    protected $fillable = [
        'administrador_transporte_id',
        'jefe_de_proceso_id',
        'trabajador_id',
        'tecnico_id',
        'orden_servicio_id',
    ];

    // Relaciones con alias
    public function AdministradorTransporte()
    {
        return $this->belongsTo(UsuariosTaller::class, 'administrador_transporte_id');
    }

    public function JefeDeProceso()
    {
        return $this->belongsTo(UsuariosTaller::class, 'jefe_de_proceso_id');
    }

    public function Trabajador()
    {
        return $this->belongsTo(UsuariosTaller::class, 'trabajador_id');
    }

    public function Tecnico()
    {
        return $this->belongsTo(UsuariosTaller::class, 'tecnico_id');
    }

    public function OrdenServicio()
    {
        return $this->belongsTo(OrdenesServicio::class,'orden_servicio_id');
    }

}
