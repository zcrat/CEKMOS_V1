<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmpleadosModel extends Model
{   use SoftDeletes;
    protected $table = 'employes';
    protected $fillable = [
        'nombre',
        'paterno',
        'materno',
        'curp',
        'rfc',
        'regimen_fiscal_id',
        'domicilio_fiscal'
    ];
    protected $casts = [];

    public function regimenfiscal()
    {
        return $this->belongsTo(RegimenesFiscalesModel::class, 'regimen_fiscal_id');
    }
}
