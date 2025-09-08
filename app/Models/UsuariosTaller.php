<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class UsuariosTaller extends Model
{
    use SoftDeletes;
    protected $table = 'usuarios_tallers';

    protected $fillable = [
        'nombre',
        'tipo_id',
    ];

    public function Tipo()
    {
        return $this->belongsTo(Tipos::class,'tipo_id');
    }
    public function AdministradoresOrdenServicio(){
        return $this->hasMany(ResponsablesOrdenServicio::class,'administrador_transporte_id');
    }
    public function JefeDeProcesoOrdenServicio(){
        return $this->hasMany(ResponsablesOrdenServicio::class,'jefe_de_proceso_id');
    }
    public function TrabajadoresOrdenServicio(){
        return $this->hasMany(ResponsablesOrdenServicio::class,'trabajador_id');
    }
    public function TecnicosOrdenServicio(){
        return $this->hasMany(ResponsablesOrdenServicio::class,'tecnico_id');
    }

}
