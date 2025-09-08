<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InterioresOrdenServicio extends Model
{

    protected $table = 'interiores_orden_servicios';
    public $timestamps=false;
    protected $fillable = [
        'orden_servicio_id',
        'puerta_interior_frontal',
        'puerta_interior_trasera',
        'puerta_delantera_frontal',
        'puerta_delantera_trasera',
        'asiento_interior_frontal',
        'asiento_interior_trasera',
        'asiento_delantera_frontal',
        'asiento_delantera_trasera',
        'consola_central',
        'claxon',
        'tablero',
        'quemacocos',
        'toldo',
        'elevadores_eletricos',
        'luces_interiores',
        'seguros_eletricos',
        'tapetes',
        'climatizador',
        'radio',
        'espejos_retrovizor',
    ];

    public function orden_servicio()
    {
        return $this->belongsTo(OrdenesServicio::class,'orden_servicio_id');
    }

    public function estatus_puerta_interior_frontal()
    {
        return $this->belongsTo(Estatus::class, 'puerta_interior_frontal');
    }

    public function estatus_puerta_interior_trasera()
    {
        return $this->belongsTo(Estatus::class, 'puerta_interior_trasera');
    }

    public function estatus_puerta_delantera_frontal()
    {
        return $this->belongsTo(Estatus::class, 'puerta_delantera_frontal');
    }

    public function estatus_puerta_delantera_trasera()
    {
        return $this->belongsTo(Estatus::class, 'puerta_delantera_trasera');
    }

    public function estatus_asiento_interior_frontal()
    {
        return $this->belongsTo(Estatus::class, 'asiento_interior_frontal');
    }

    public function estatus_asiento_interior_trasera()
    {
        return $this->belongsTo(Estatus::class, 'asiento_interior_trasera');
    }

    public function estatus_asiento_delantera_frontal()
    {
        return $this->belongsTo(Estatus::class, 'asiento_delantera_frontal');
    }

    public function estatus_asiento_delantera_trasera()
    {
        return $this->belongsTo(Estatus::class, 'asiento_delantera_trasera');
    }

    public function estatus_consola_central()
    {
        return $this->belongsTo(Estatus::class, 'consola_central');
    }

    public function estatus_claxon()
    {
        return $this->belongsTo(Estatus::class, 'claxon');
    }

    public function estatus_tablero()
    {
        return $this->belongsTo(Estatus::class, 'tablero');
    }

    public function estatus_quemacocos()
    {
        return $this->belongsTo(Estatus::class, 'quemacocos');
    }

    public function estatus_toldo()
    {
        return $this->belongsTo(Estatus::class, 'toldo');
    }

    public function estatus_elevadores_eletricos()
    {
        return $this->belongsTo(Estatus::class, 'elevadores_eletricos');
    }

    public function estatus_luces_interiores()
    {
        return $this->belongsTo(Estatus::class, 'luces_interiores');
    }

    public function estatus_seguros_eletricos()
    {
        return $this->belongsTo(Estatus::class, 'seguros_eletricos');
    }

    public function estatus_tapetes()
    {
        return $this->belongsTo(Estatus::class, 'tapetes');
    }

    public function estatus_climatizador()
    {
        return $this->belongsTo(Estatus::class, 'climatizador');
    }

    public function estatus_radio()
    {
        return $this->belongsTo(Estatus::class, 'radio');
    }

    public function estatus_espejos_retrovizor()
    {
        return $this->belongsTo(Estatus::class, 'espejos_retrovizor');
    }

}
