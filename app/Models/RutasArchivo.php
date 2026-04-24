<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class RutasArchivo extends Model
{
    use SoftDeletes;
    protected $table = 'rutas_archivos';
    protected $fillable = [
        'disk','folder','tipo_id','estatus_id'
    ];
    public function tipo()
    {
        return $this->belongsTo(Tipos::class, 'tipo_id');
    }
    public function estatus()
    {
        return $this->belongsTo(Estatus::class, 'estatus_id');
    }
}
