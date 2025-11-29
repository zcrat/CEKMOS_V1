<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EscapeInspeccionVehicular extends Model
{
    protected $table = 'escape_inspeccion_vehicular';
    protected $fillable = [
        'mofle_convertidor_catlitico',
        'sensores_soporte_tubos',
        'notas'
    ];
    protected $cast=[
    ];
    public function mofle_convertidor_catlitico_estatus(){
        return $this->hasMany(Estatus::class, 'mofle_convertidor_catlitico');
    }
    public function sensores_soporte_tubos_estatus(){
        return $this->hasMany(Estatus::class, 'sensores_soporte_tubos');
    }
}
