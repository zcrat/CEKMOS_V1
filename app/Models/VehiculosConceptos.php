<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VehiculosConceptos extends Model
{
    protected $table = 'vehiculos_conceptos';
    protected $fillable = [
        'descripcion',
    ];
}
