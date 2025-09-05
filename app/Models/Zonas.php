<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Zonas extends Model
{
    protected $table = 'zonas';
    protected $fillable = [
        'descripcion',
    ];
}
