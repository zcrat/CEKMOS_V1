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

    public function Cfdi_disponibles()
    {
        return $this->hasMany(CFDDisponiblesModel::class,'regimen_fiscal_id','clave');
    }
    public function Empleados()
    {
        return $this->hasMany(EmpleadosModel::class,'regimen_fiscal_id','clave');
    }
    public function Empresas()
    {
        return $this->hasMany(Empresas::class, 'regimen_id');
    }

}