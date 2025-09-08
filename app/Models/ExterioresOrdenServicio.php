<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class ExterioresOrdenServicio extends Model
{
    use SoftDeletes;
    protected $table = 'exteriores_orden_servicios';

    protected $fillable = [
        'orden_servicio_id',
        'antena_radio',
        'antena_telefono',
        'antena_cb',
        'estribos',
        'espejos_laterales',
        'guardafangos',
        'parabrisas',
        'sistema_alarma',
        'limpia_parabrisas',
        'luces_exteriores',
    ];

    // Relaciones
    public function OrdenServicio()
    {
        return $this->belongsTo(OrdenesServicio::class,'orden_servicio_id');
    }

    public function EstatusAntenaRadio()
    {
        return $this->belongsTo(Estatus::class, 'antena_radio');
    }

    public function EstatusAntenaTelefono()
    {
        return $this->belongsTo(Estatus::class, 'antena_telefono');
    }

    public function EstatusAntenaCB()
    {
        return $this->belongsTo(Estatus::class, 'antena_cb');
    }

    public function EstatusEstribos()
    {
        return $this->belongsTo(Estatus::class, 'estribos');
    }

    public function EstatusEspejosLaterales()
    {
        return $this->belongsTo(Estatus::class, 'espejos_laterales');
    }

    public function EstatusGuardafangos()
    {
        return $this->belongsTo(Estatus::class, 'guardafangos');
    }

    public function EstatusParabrisas()
    {
        return $this->belongsTo(Estatus::class, 'parabrisas');
    }

    public function EstatusSistemaAlarma()
    {
        return $this->belongsTo(Estatus::class, 'sistema_alarma');
    }

    public function EstatusLimpiaParabrisas()
    {
        return $this->belongsTo(Estatus::class, 'limpia_parabrisas');
    }

    public function EstatusLucesExteriores()
    {
        return $this->belongsTo(Estatus::class, 'luces_exteriores');
    }

}
