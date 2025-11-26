<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CFDIModel extends Model
{
    use SoftDeletes;
    protected $table = 'cfdis';
    protected $fillable = [
        'usocfdi',
        'descripcion',
        'persona_fisica',
        'persona_moral',
    ];
     protected $casts = [
        'persona_fisica'=>'boolean',
        'persona_moral'=>'boolean',
     ];
    
    public function regimenes_fiscales_disponibles(){
        return $this->hasMany(CFDDisponiblesModel::class,'uso_cfdi_id','id');
    }
}
