<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Vehiculos extends Model
{   
    use SoftDeletes;
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
    protected $casts=[];

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
    public function ordenes_servicio()
    {
        return $this->hasMany(OrdenesServicio::class, 'vehiculo_id');
    }
}