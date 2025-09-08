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
    public function categoria()
    {
        return $this->belongsTo(Categorias::class, 'categoria_id');
    }

    public function notificaciones()
    {
        return $this->hasMany(Notificaciones::class, 'tipo_id');
    }
    public function vehiculos()
    {
        return $this->hasMany(Vehiculos::class, 'tipo_id');
    }
    public function RutasArchivo()
    {
        return $this->hasMany(RutasTiposArchivo::class, 'tipo_id');
    }
    public function ArchivosOrdenServicio()
    {
        return $this->hasMany(ArchivosOrdenServicio::class, 'tipo_id');
    }
    public function Presupuestos(){
        return $this->hasMany(Presupuestos::class,'tipo_id');
    }
}
