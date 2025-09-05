<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ModuloOrdenesServicio extends Model
{       
    protected $table = 'modulos';
    protected $fillable = [
        'descripcion',
        'clave',
        'modulo_id',
        'contrato_id',
        'zona_id',
        'año',
    ];
    public function modulo()
    {
        return $this->belongsTo(Emisor::class, 'modulo_id');
    }
    public function zona()
    {
        return $this->belongsTo(Emisor::class, 'contrato_id');
    }
    public function contrato()
    {
        return $this->belongsTo(Emisor::class, 'zona_id');
    }

}
