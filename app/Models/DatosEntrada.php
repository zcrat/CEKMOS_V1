<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class DatosEntrada extends Model
{
    use SoftDeletes;
    protected $table = 'datos_entradas';

    protected $fillable = [
        'fecha',
        'estimacion',
        'kilomentraje',
        'gasolina',
        'orden_servicio_id',
    ];

    // Relaciones
    public function nivelCombustible()
    {
        return $this->belongsTo(NivelesCombustible::class, 'gasolina');
    }

    public function ordenServicio()
    {
        return $this->belongsTo(OrdenesServicio::class, 'orden_servicio_id');
    }

}
