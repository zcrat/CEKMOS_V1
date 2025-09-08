<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class RegimenesFiscalesModel extends Model
{
    use SoftDeletes;
    protected $table = 'regimes_fiscales';
    protected $primaryKey = 'clave';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = [
        'clave',
        'descripcion',
        'persona_fisica',
        'persona_moral',
    ];
    protected $casts = [];

    public function cfdi_disponibles()
    {
        return $this->hasMany(CFDDisponiblesModel::class,'regimen_fiscal_id','clave');
    }
    public function empleados()
    {
        return $this->hasMany(EmpleadosModel::class,'regimen_fiscal_id','clave');
    }
    public function empresas()
    {
        return $this->hasMany(Empresas::class, 'regimen_fiscal_id','clave');
    }

}