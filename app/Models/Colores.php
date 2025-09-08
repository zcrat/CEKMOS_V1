<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Colores extends Model
{
    use SoftDeletes;
    protected $table = 'colores';
    protected $fillable = [
    'descripcion',
    ];

    public function vehiculos(){
        return $this->hasMany(Vehiculos::class,'color_id');
    }
}
