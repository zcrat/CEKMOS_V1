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
        'numero',
        'monto',
    ];

    protected $casts=[
        'monto'=>'decimal:10,2',
    ];
    public function modulos_ordenes_servicio(){
        return $this->hasMany(ModuloOrdenesServicio::class,'contrato_id');
    }
}
