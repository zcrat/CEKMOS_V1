<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class NivelesCombustible extends Model
{
    use SoftDeletes;
    protected $table = 'niveles_combustible';
    protected $fillable = [
    'descripcion',
    ];

    public function datos_entrada()
    {
        return $this->hasMany(DatosEntrada::class, 'gasolina');
    }

    public function datos_salida()
    {
        return $this->hasMany(DatosSalida::class, 'gasolina');
    }
}
