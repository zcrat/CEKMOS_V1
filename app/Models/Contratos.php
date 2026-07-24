<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Contratos extends Model
{
    use SoftDeletes;
    protected $table = 'contratos';
    protected $fillable = [
        'descripcion',
        'tipo',
        'numero',
        'monto',
        'modulo_id',
        'zona_id',
        'año',
    ];

    protected $casts=[
        'monto'=>'decimal:2',
    ];
    public function modulos_ordenes_servicio(){
        return $this->hasMany(ModuloOrdenesServicio::class,'contrato_id');
    }
    public function modulo(){
        return $this->belongsTo(Modulos::class,'modulo_id');
    }
    public function zona(){
        return $this->belongsTo(Zonas::class,'zona_id');
    }
}
