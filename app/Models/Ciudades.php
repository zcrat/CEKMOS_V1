<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Ciudades extends Model
{
    use SoftDeletes;
    protected $table = 'ciudades';
    protected $fillable = [
        'descripcion',
        'estado_id'
    ];
    public function estado(){
        return $this->belongsTo(Estados::class, 'estado_id');
    }
    public function clientes(){
        return $this->hasMany(Clientes::class, 'ciudad_id');
    }
    public function empresas(){
        return $this->hasMany(Empresas::class, 'ciudad_id');
    }
    
}
