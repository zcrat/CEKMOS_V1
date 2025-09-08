<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Marcas extends Model
{
    use SoftDeletes;
    protected $table = 'marcas';
    protected $fillable = [
        'descripcion',
    ];

     public function modelos()
    {
        return $this->hasMany(Modelos::class, 'marca_id');
    }
}
