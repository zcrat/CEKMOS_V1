<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Empresas extends Model
{
    use SoftDeletes;
    protected $table = 'empresas';
    protected $fillable = [
        'nombre',
        'rfc',
        'email',
        'logo',
        'calle',
        'cp',
        'ciudad_id',
        'user_id',
        'regimen_fiscal_id',
        'telefono',
        'tel_celular',
        'tel_negocio',
    ];
    protected $casts = [];
    
    public function user(){
        return $this->belongsTo(User::class,'user_id');
    }
    public function ciudad(){
        return $this->belongsTo(Ciudades::class,'ciudad_id');
    }
    public function regimen(){
        return $this->belongsTo(RegimenesFiscales::class,'regimen_id');
    }
    public function clientes(){
        return $this->hasMany(Clientes::class,'empresa_id');
    }
    public function ordenes_Servicio()
    {
        return $this->hasMany(OrdenesServicio::class, 'empresa_id');
    }
    public function facturas()
    {
        return $this->hasMany(Facturas::class, 'empresa_id');
    }
}
