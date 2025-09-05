<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vehiculos extends Model
{
    protected $table = 'vehiculos';
    protected $fillable = [
        'placas',
        'año',
        'economico',
        'vin',
        'tipo_id',
        'color_id',
        'modelo_id',
    ];
    public function tipo()
    {
        return $this->belongsTo(Tipos::class, 'tipo_id');
    }
    public function color()
    {
        return $this->belongsTo(Colores::class, 'color_id');
    }
    public function modelo()
    {
        return $this->belongsTo(Modelos::class, 'modelo_id');
    }
}