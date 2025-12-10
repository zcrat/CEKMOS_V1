<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CajaMovimientos extends Model
{
    protected $table = 'caja_movimientos';

    protected $fillable = [
        'descripcion',
        'ingreso',
        'egreso',
        'fecha',
        'user_id'
    ];
     protected $cast=[
        'ingreso'=>'decimal:2',
        'egreso'=>'decimal:2',
        'fecha'=>'datetime',
    ];
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
   
}
