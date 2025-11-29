<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LucesEspiasInspeccionVehicular extends Model
{
    protected $table = 'luces_espias_inspeccion_vehicular';
    protected $fillable = [
        'codigo',
        'notas'
    ];
    protected $cast=[
    ];
    public function codigo_estatus(){
        return $this->hasMany(Estatus::class, 'codigo');
    }
}
