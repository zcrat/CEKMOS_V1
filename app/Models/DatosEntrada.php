<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DatosEntrada extends Model
{
    public $timestamps = false;
    protected $table = 'datos_entradas';

    protected $fillable = [
        'fecha',
        'estimacion',
        'kilomentraje',
        'gasolina',
        'orden_servicio_id',
    ];

    protected $casts = [
        'fecha' => 'datetime',
        'estimacion' => 'datetime',
        'kilomentraje' => 'decimal:10,2',
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
