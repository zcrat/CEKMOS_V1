<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NivelesCombustible extends Model
{
    protected $table = 'niveles_combustible';
    protected $fillable = [
    'descripcion',
    ];
}
