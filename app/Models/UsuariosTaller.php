<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class UsuariosTaller extends Model
{
    use SoftDeletes;
    protected $table = 'usuarios_taller';

    protected $fillable = [
        'nombre',
        'tipo_id',
    ];

    public function tipo()
    {
        return $this->belongsTo(Tipos::class,'tipo_id');
    }
    public function administradores_ordenes_servicio(){
        return $this->hasMany(ResponsablesOrdenServicio::class,'administrador_transporte_id');
    }
    public function jefeDeProceso_ordenes_servicio(){
        return $this->hasMany(ResponsablesOrdenServicio::class,'jefe_de_proceso_id');
    }
    public function trabajadores_ordenes_servicio(){
        return $this->hasMany(ResponsablesOrdenServicio::class,'trabajador_id');
    }
    public function tecnicos_ordenes_servicio(){
        return $this->hasMany(ResponsablesOrdenServicio::class,'tecnico_id');
    }

}
