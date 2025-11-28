<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Tipos extends Model
{
    use SoftDeletes;
    protected $table = 'tipos';
    protected $fillable = [
        'descripcion',
        'categoria_id'
    ];
    public function archivos(){
        return $this->hasMany(Archivos::class, 'tipo_id');
    }
    public function categoria(){
        return $this->belongsTo(Categorias::class, 'categoria_id');
    }
    public function usuarios_taller(){
        return $this->hasMany(UsuariosTaller::class,'tipo_id');
    }
    public function presupuestos(){
        return $this->hasMany(Presupuestos::class,'tipo_id');
    }
    public function vehiculos(){
        return $this->hasMany(Vehiculos::class, 'tipo_id');
    }
    public function rutas_archivo(){
        return $this->hasOne(RutasTiposArchivo::class, 'tipo_id');
    }
    public function facturas(){
        return $this->hasMany(Facturas::class, 'tipo_id');
    }
    public function conceptos_presupuesto(){
        return $this->hasMany(ConceptosPresupuestos::class, 'tipo_id');
    }
    public function notificaciones(){
        return $this->hasMany(Notificaciones::class, 'tipo_id');
    }
    public function log_acciones(){
        return $this->hasMany(LogAccionesOS::class,'tipo_id');
    }
}
