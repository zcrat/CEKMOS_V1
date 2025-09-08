<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class ArchivosOrdenServicio extends Model
{
    use SoftDeletes;
    protected $table = 'archivos_orden_servicios';

    protected $fillable = [
        'nombre',
        'orden_servicio_id',
        'tipo_id',
    ];

    // Relaciones
    public function orden_servicio()
    {
        return $this->belongsTo(OrdenesServicio::class, 'orden_servicio_id');
    }

    public function tipo()
    {
        return $this->belongsTo(Tipos::class, 'tipo_id');
    }

}
