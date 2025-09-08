<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use PDO;

class Colores extends Model
{
    use SoftDeletes;
    protected $table = 'colores';
    protected $fillable = [
    'descripcion',
    ];

    public function Vehiculos(){
        return $this->hasMany(Vehiculos::class,'color_id');
    }
}
