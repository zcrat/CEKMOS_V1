<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subcontrato extends Model
{
    public $timestamps = false;

    protected $table = 'subcontratos';

    protected $fillable = [
        'orden_servicio_id',
        'fecha_inicio',
        'fecha_fin',
        'user_id',
    ];

    protected $casts = [
        'fecha_inicio' => 'datetime',
        'fecha_fin' => 'datetime',
    ];

    public function ordenServicio()
    {
        return $this->belongsTo(OrdenesServicio::class, 'orden_servicio_id');
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
