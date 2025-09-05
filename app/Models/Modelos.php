<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Modelos extends Model
{
    protected $table = 'marcas';
    protected $fillable = [
        'descripcion',
        'marca_id'
    ];
    public function marca()
    {
        return $this->belongsTo(Marcas::class, 'marca_id');
    }
}
