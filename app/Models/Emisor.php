<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Emisor extends Model
{
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
}
