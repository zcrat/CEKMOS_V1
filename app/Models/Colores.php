<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Colores extends Model
{
    protected $table = 'colores';
    protected $fillable = [
    'descripcion',
    ];
}
