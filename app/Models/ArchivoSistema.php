<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ArchivoSistema extends Model
{
    protected $table = 'archivos_sistema';

    protected $fillable = [
        'nombre_archivo',
        'tipo_archivo',
        'disco',
        'ruta_archivo',
        'ruta_resultado',
        'usuario_id',
        'estatus_resultante',
        'detalles_procesamiento',
        'datos_entrada',
    ];

    protected $casts = [
        'datos_entrada' => 'array',
        'detalles_procesamiento' => 'array',
    ];

    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id')->withTrashed();
    }

    public function conceptos_presupuestos()
    {
        return $this->hasMany(ConceptosPresupuestos::class, 'archivo_sistema_id');
    }
}
