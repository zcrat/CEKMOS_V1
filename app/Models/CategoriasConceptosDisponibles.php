<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CategoriasConceptosDisponibles extends Model
{
    protected $table = 'categorias_conceptos_disponibles';

    protected $fillable = [
        'tipo_presupuesto_id',
        'categoria_concepto_id',
    ];

    public function tipo_presupuesto()
    {
        return $this->belongsTo(Tipos::class, 'tipo_presupuesto_id');
    }

    public function categoria_concepto()
    {
        return $this->belongsTo(Tipos::class, 'categoria_concepto_id');
    }
}
