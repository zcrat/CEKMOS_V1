<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Categorias extends Model
{   
    use SoftDeletes;
    protected $table = 'categorias';
    protected $fillable = [
    'descripcion',
    ];

    public function tipos()
    {
        return $this->hasMany(Tipos::class, 'categoria_id');
    }
    public function estatus()
    {
        return $this->hasMany(Estatus::class, 'categoria_id');
    }
}
