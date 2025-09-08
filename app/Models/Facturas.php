<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Facturas extends Model
{
    protected $table = 'facturas';

    protected $fillable = [
        'empresa_id',
        'emisor_id',
        'user_id',
        'xml',
        'pdf',
        'acuse',
        'estatus_id',
        'monto',
        'folio',
    ];

    // Relaciones
    public function Empresa()
    {
        return $this->belongsTo(Empresas::class,'empresa_id');
    }

    public function Emisor()
    {
        return $this->belongsTo(Emisor::class,'emisor_id');
    }

    public function User()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function Estatus()
    {
        return $this->belongsTo(Estatus::class,'estatus_id');
    }
    public function Presupuestos(){
        return $this->hasMany(Presupuestos::class,'factura_id');
    }


}
