<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SistemaModelos extends Model
{
    protected $fillable = [
        'nombre','descripcion'
    ];

    public function bitacora(){
        return $this->hasMany(BitacoraAccionesUsers::class,'sistema_modelo_id');
    }

}
