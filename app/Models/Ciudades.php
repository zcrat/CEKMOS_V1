<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Ciudades extends Model
{
    use SoftDeletes;
    protected $table = 'ciudades';
    protected $fillable = [
        'descripcion',
        'estado_id'
    ];
    public function Estado()
    {
        return $this->belongsTo(Estados::class, 'estado_id');
    }
    public function Empresas()
    {
        return $this->hasMany(Empresas::class, 'ciudad_id');
    }
    public function Clientes()
    {
        return $this->hasMany(Clientes::class, 'ciudad_id');
    }

}
