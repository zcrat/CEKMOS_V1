<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
USE Illuminate\Database\Eloquent\SoftDeletes;
class Zonas extends Model
{
    use SoftDeletes;
    protected $table = 'zonas';
    protected $fillable = [
        'descripcion',
    ];
    public function modulos_ordenes_servicio(){
        return $this->hasMany(ModuloOrdenesServicio::class,'zona_id');
    }
}
