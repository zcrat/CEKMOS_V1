<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDelete;
use Illuminate\Database\Eloquent\SoftDeletes;

class InterioresOrdenServicio extends Model
{
    use SoftDeletes;
     protected $table = 'interiores_orden_servicios';

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

    public function OrdenServicio()
    {
        return $this->belongsTo(OrdenesServicio::class,'orden_servicio_id');
    }

    public function EstatusPuertaInteriorFrontal()
    {
        return $this->belongsTo(Estatus::class, 'puerta_interior_frontal');
    }

    public function EstatusPuertaInteriorTrasera()
    {
        return $this->belongsTo(Estatus::class, 'puerta_interior_trasera');
    }

    public function EstatusPuertaDelanteraFrontal()
    {
        return $this->belongsTo(Estatus::class, 'puerta_delantera_frontal');
    }

    public function EstatusPuertaDelanteraTrasera()
    {
        return $this->belongsTo(Estatus::class, 'puerta_delantera_trasera');
    }

    public function EstatusAsientoInteriorFrontal()
    {
        return $this->belongsTo(Estatus::class, 'asiento_interior_frontal');
    }

    public function EstatusAsientoInteriorTrasera()
    {
        return $this->belongsTo(Estatus::class, 'asiento_interior_trasera');
    }

    public function EstatusAsientoDelanteraFrontal()
    {
        return $this->belongsTo(Estatus::class, 'asiento_delantera_frontal');
    }

    public function EstatusAsientoDelanteraTrasera()
    {
        return $this->belongsTo(Estatus::class, 'asiento_delantera_trasera');
    }

    public function EstatusConsolaCentral()
    {
        return $this->belongsTo(Estatus::class, 'consola_central');
    }

    public function EstatusClaxon()
    {
        return $this->belongsTo(Estatus::class, 'claxon');
    }

    public function EstatusTablero()
    {
        return $this->belongsTo(Estatus::class, 'tablero');
    }

    public function EstatusQuemacocos()
    {
        return $this->belongsTo(Estatus::class, 'quemacocos');
    }

    public function EstatusToldo()
    {
        return $this->belongsTo(Estatus::class, 'toldo');
    }

    public function EstatusElevadoresEletricos()
    {
        return $this->belongsTo(Estatus::class, 'elevadores_eletricos');
    }

    public function EstatusLucesInteriores()
    {
        return $this->belongsTo(Estatus::class, 'luces_interiores');
    }

    public function EstatusSegurosEletricos()
    {
        return $this->belongsTo(Estatus::class, 'seguros_eletricos');
    }

    public function EstatusTapetes()
    {
        return $this->belongsTo(Estatus::class, 'tapetes');
    }

    public function EstatusClimatizador()
    {
        return $this->belongsTo(Estatus::class, 'climatizador');
    }

    public function EstatusRadio()
    {
        return $this->belongsTo(Estatus::class, 'radio');
    }

    public function EstatusEspejosRetrovizor()
    {
        return $this->belongsTo(Estatus::class, 'espejos_retrovizor');
    }

}
