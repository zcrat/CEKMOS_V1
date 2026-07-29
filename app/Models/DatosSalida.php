<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DatosSalida extends Model
{
    use SoftDeletes;

    protected $table = 'datos_salidas';

    public $timestamps = false;

    protected $fillable = [
        'fecha',
        'kilometraje',
        'gasolina',
        'orden_servicio_id',
    ];

    protected $casts = [
        'fecha' => 'datetime',
        'kilometraje' => 'integer',
    ];

    public function nivel_combustible()
    {
        return $this->belongsTo(NivelesCombustible::class, 'gasolina');
    }

    public function orden_servicio()
    {
        return $this->belongsTo(OrdenesServicio::class, 'orden_servicio_id');
    }
}
