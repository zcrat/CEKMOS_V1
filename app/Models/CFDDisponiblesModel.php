<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class CFDDisponiblesModel extends Model
{
    use SoftDeletes;
    protected $table = 'cfdis_disponibles';
    protected $fillable = [
        'regimen_fiscal_id',
        'uso_cfdi_id',
    ];
    protected $casts = [];

    public function Regimenfiscal()
    {
        return $this->belongsTo(RegimenesFiscalesModel::class, 'regimen_fiscal_id');
    }
    public function Usocfdi()
    {
        return $this->belongsTo(CFDIModel::class, 'uso_cfdi_id');
    }
}
