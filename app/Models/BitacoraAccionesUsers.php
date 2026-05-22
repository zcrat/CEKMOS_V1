<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BitacoraAccionesUsers extends Model
{
    protected $fillable = [
        'tipo_id',
        'user_id',
        'sistema_modelo_id',
        'datos_enviados',
        'datos_iniciales',
    ];

    protected $casts = [
        'datos_enviados'=>'array',
        'datos_iniciales'=>'array',
    ];


    public function tipo() : BelongsTo {
        return $this->belongsTo(Tipos::class,'tipo_id');
    }
    public function user() : BelongsTo {
        return $this->belongsTo(User::class,'user_id');
    }
    public function sistema_modulo() : BelongsTo {
        return $this->belongsTo(SistemaModelos::class,'sistema_modelo_id');
    }
}
