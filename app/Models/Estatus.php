<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Estatus extends Model
{
   protected $table = 'estatus';
    protected $fillable = [
        'descripcion',
        'categoria_id'
    ];
    public function categoria()
    {
        return $this->belongsTo(Categorias::class, 'categoria_id');
    }
}
