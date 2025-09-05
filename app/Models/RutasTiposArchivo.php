<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RutasTiposArchivo extends Model
{
    protected $table = 'rutas_tipos_archivos';
    protected $fillable = [
        'descripcion',
        'tipo_id'
    ];
    public function tipo()
    {
        return $this->belongsTo(Tipos::class, 'tipo_id');
    }
}
