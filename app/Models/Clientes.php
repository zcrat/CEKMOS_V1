<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Clientes extends Model
{
   use SoftDeletes;
    protected $table = 'clientes';
    protected $fillable = [
        'empresa_id',
        'nombre',
        'calle',
        'cp',
        'ciudad_id',
        'user_id',
        'email',
        'telefono',
        'tel_celular',
        'tel_negocio',
    ];
    protected $casts = [];
    
    public function User(){
        return $this->belongsTo(User::class,'user_id');
    }
    public function Ciudad(){
        return $this->belongsTo(Ciudades::class,'ciudad_id');
    }
    public function Empresa(){
        return $this->belongsTo(Empresas::class,'empresa_id');
    }
    public function OrdenesServicios()
    {
        return $this->hasMany(OrdenesServicio::class, 'cliente_id');
    }
}
