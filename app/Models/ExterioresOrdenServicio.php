<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
class ExterioresOrdenServicio extends Model
{
    protected $table = 'exteriores_orden_servicios';
    public $timestamps=false;
    protected $fillable = [
        'orden_servicio_id',
        'antena_radio',
        'estribos',
        'antena_telefono',
        'guardafangos',
        'antena_cb',
        'parabrisas',
        'sistema_alarma',
        'limpia_parabrisas',
        'luces_exteriores',
        'espejos_laterales',
    ];

    // Relaciones
    public function orden_servicio()
    {
        return $this->belongsTo(OrdenesServicio::class,'orden_servicio_id');
    }

    public function estatus_antena_radio()
    {
        return $this->belongsTo(Estatus::class, 'antena_radio');
    }

    public function estatus_antena_telefono()
    {
        return $this->belongsTo(Estatus::class, 'antena_telefono');
    }

    public function estatus_antena_cb()
    {
        return $this->belongsTo(Estatus::class, 'antena_cb');
    }

    public function estatus_estribos()
    {
        return $this->belongsTo(Estatus::class, 'estribos');
    }

    public function estatus_espejos_laterales()
    {
        return $this->belongsTo(Estatus::class, 'espejos_laterales');
    }

    public function estatus_guardafangos()
    {
        return $this->belongsTo(Estatus::class, 'guardafangos');
    }

    public function estatus_parabrisas()
    {
        return $this->belongsTo(Estatus::class, 'parabrisas');
    }

    public function estatus_sistema_alarma()
    {
        return $this->belongsTo(Estatus::class, 'sistema_alarma');
    }

    public function estatus_limpia_parabrisas()
    {
        return $this->belongsTo(Estatus::class, 'limpia_parabrisas');
    }

    public function estatus_luces_exteriores()
    {
        return $this->belongsTo(Estatus::class, 'luces_exteriores');
    }

}
