<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
class CondicionesPinturaOrdenServicio extends Model
{

    protected $table = 'condiciones_pintura_orden_servicios';
    public $timestamps=false;
    protected $fillable = [
        'orden_servicio_id',
        'decolorada',
        'emblemas_completos',
        'color_no_igual',
        'logos',
        'exeso_rayones',
        'exeso_rociado',
        'pequenias_grietas',
        'danios_granizado',
        'carroceria_golpes',
        'lluvia_acido',
    ];

    protected $casts = [
        'decolorada' => 'boolean',
        'emblemas_completos' => 'boolean',
        'color_no_igual' => 'boolean',
        'logos' => 'boolean',
        'exeso_rayones' => 'boolean',
        'exeso_rociado' => 'boolean',
        'pequenias_grietas' => 'boolean',
        'danios_granizado' => 'boolean',
        'carroceria_golpes' => 'boolean',
        'lluvia_acido' => 'boolean',
    ];

    // Relación principal
    public function orden_servicio()
    {
        return $this->belongsTo(OrdenesServicio::class,'orden_servicio_id');
    }

}
