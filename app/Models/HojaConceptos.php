<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HojaConceptos extends Model
{
    protected $table = 'hoja_conceptos';
    protected $fillable = [
        'new',
        'cantidad',
        'costo',
        'venta',
        'tipo_id',
        'concepto_id',
        'orden_servicio_id',
    ];
    protected $casts=[
        'new'=>'boolean',
        'cantidad'=>'decimal:8,2',
        'costo'=>'decimal:8,2',
        'venta'=>'decimal:8,2',
    ];
    
    public function orden_servicio(){
        return $this->belongsTo(OrdenesServicio::class,'orden_servicio_id');
    }
    public function concepto(){
        return $this->belongsTo(ConceptosAlmacen::class,'concepto_id');
    }
    public function tipo(){
        return $this->belongsTo(Tipos::class,'tipo_id');
    }
}
