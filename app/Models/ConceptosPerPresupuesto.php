<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConceptosPerPresupuesto extends Model
{
    protected $table="conceptos_per_presupuestos";
    protected $fillable = [
        'cantidad',
        'costo',
        'venta',
        'presupuesto_id',
        'concepto_presupuesto_id',
        'user_id',
    ];
    protected $cast=[
        'cantidad'=>'decimal:2',
        'costo'=>'decimal:2',
        'venta'=>'decimal:2',
    ];

    public function presupuesto(){
       return $this->belongsTo(Presupuestos::class,'presupuesto_id');
    } 
    public function concepto_presupuesto(){
       return $this->belongsTo(ConceptosPresupuestos::class,'concepto_presupuesto_id');
    } 
    public function user(){
       return $this->belongsTo(User::class,'user_id');
    } 
}
