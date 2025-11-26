<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CategoriasSat extends Model
{
    protected $table = 'categorias_sat';
    protected $fillable = [
        'descripcion',
        'codigo_sat',
    ];

    public function conceptos_presupuestos(){
        return $this->hasMany(ConceptosPresupuestos::class,'categoria_sat_id');
    }
}
