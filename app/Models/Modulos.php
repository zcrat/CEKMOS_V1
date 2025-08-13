<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Modulos extends Model
{
    use SoftDeletes;
   protected $table = 'modulos';
    protected $fillable = [
    'nombre',
    ];
}
