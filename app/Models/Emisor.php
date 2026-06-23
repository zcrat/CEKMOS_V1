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
        // Dirección
        'calle',
        'colonia',
        'ciudad',
        'estado',
        'cp',
        'telefono',
        'logotipo',
        'regimen',
        'codigo',
        'serie_factura',
        'serie_p_factura',
    ];
    // Relación actual: un Emisor pertenece a muchas asignaciones de módulo/orden
    public function modulo_ordenes_servicios(){
        return $this->hasMany(ModuloOrdenesServicio::class,'emisor_id');
    }
    public function facturas()
    {
        return $this->hasMany(Facturas::class, 'emisor_id');
    }
}
