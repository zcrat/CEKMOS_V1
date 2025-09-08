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
    
    public function User(){
        return $this->belongsTo(User::class,'user_id');
    }
    public function Ciudad(){
        return $this->belongsTo(Ciudades::class,'ciudad_id');
    }
    public function Regimen(){
        return $this->belongsTo(RegimenesFiscalesModel::class,'regimen_id');
    }
    public function Clientes(){
        return $this->hasMany(Clientes::class,'empresa_id');
    }
    public function OrdenesServicios()
    {
        return $this->hasMany(OrdenesServicio::class, 'empresa_id');
    }
    public function Facturas()
    {
        return $this->hasMany(Facturas::class, 'empresa_id');
    }
}
