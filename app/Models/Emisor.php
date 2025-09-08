<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Emisor extends Model
{
    use SoftDeletes;
    protected $table = 'emisores';
    protected $fillable = [
        'n_certificado',
        'archivo_cer',
        'archivo_key',
        'clave_key',
        'rfc',
        'nombre',
        'logotipo',
        'regimen',
        'codigo',
        'serie_factura',
        'serie_p_factura',
    ];
    public function modulos(){
        return $this->hasMany(Modulos::class,'emisor_id');
    }
    public function facturas()
    {
        return $this->hasMany(Facturas::class, 'emisor_id');
    }
}
