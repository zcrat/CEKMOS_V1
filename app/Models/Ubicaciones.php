<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Ubicaciones extends Model
{
    use SoftDeletes;
    protected $table = 'ubicaciones';
    protected $fillable = [
        'descripcion',
        'nombre'
    ];

    public function OrdenesServicio(){
        return $this->hasMany(OrdenesServicio::class,'color_id');
    }
}
