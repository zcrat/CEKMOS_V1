<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UnidadesSat extends Model
{
    protected $table = 'unidades_sat';
    protected $fillable = [
    'descripcion',
    'codigo',
    ];

    public function conceptos_presupuestos(){
        return $this->hasMany(ConceptosPresupuestos::class,'unidad_sat_id');
    }
}
