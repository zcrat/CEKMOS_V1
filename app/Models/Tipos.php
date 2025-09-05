<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tipos extends Model
{
    protected $table = 'tipos';
    protected $fillable = [
        'descripcion',
        'categoria_id'
    ];
    public function categoria()
    {
        return $this->belongsTo(Categorias::class, 'categoria_id');
    }
}
