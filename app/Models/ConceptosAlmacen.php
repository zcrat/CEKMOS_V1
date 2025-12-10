<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConceptosAlmacen extends Model
{
    protected $table = 'conceptos_almacen';
    protected $fillable = [
        'clave',
        'descripcion',
        'costo',
        'venta',
    ];
    protected $casts=[
        'costo'=>'decimal:8,2',
        'venta'=>'decimal:8,2',
    ];
   
    public function vales_almacen(){
        return $this->hasMany(ConceptosPerValeAlmacen::class,'concepto_id');
    }
    public function hojas_conceptos(){
        return $this->hasMany(HojaConceptos::class,'concepto_id');
    }
}
