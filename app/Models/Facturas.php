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

    protected $casts=[
        'monto' => 'decimal:10,2'
    ];
    public function empresa()
    {
        return $this->belongsTo(Empresas::class,'empresa_id');
    }

    public function emisor()
    {
        return $this->belongsTo(Emisor::class,'emisor_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function estatus()
    {
        return $this->belongsTo(Estatus::class,'estatus_id');
    }
    public function presupuestos(){
        return $this->hasMany(Presupuestos::class,'factura_id');
    }


}
