<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Modelos extends Model
{   
    use SoftDeletes;
    protected $table = 'modelos';
    protected $fillable = [
        'descripcion',
        'marca_id',
        'motor_id',
    ];
    public function marca()
    {
        return $this->belongsTo(Marcas::class, 'marca_id');
    }
    public function motor()
    {
        return $this->belongsTo(Motores::class, 'motor_id');
    }
    public function vehiculos(){
        return $this->hasMany(Vehiculos::class,'modelo_id');
    }
}
