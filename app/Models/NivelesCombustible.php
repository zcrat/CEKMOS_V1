<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class NivelesCombustible extends Model
{
    use SoftDeletes;
    protected $table = 'niveles_combustible';
    protected $fillable = [
    'descripcion',
    ];
}
