<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
class DatosSalida extends Model
{
    protected $table = 'datos_salidas';
    public $timestamps = false;
    protected $fillable = [
        'fecha',
        'kilomentraje',
        'gasolina',
        'orden_servicio_id',
    ];
    protected $casts = [
        'fecha' => 'datetime',
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
