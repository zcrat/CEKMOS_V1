<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class RutasTiposArchivo extends Model
{
    use SoftDeletes;
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
