<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ValesAlmacen extends Model
{
    use SoftDeletes;
    protected $table = 'vales_almacen';
    protected $fillable = [
        'destino',
        'motor',
        'user_id',
        'orden_servicio_id',
        'tipo',
        'status',
    ];
    protected $casts=[
        'tipo' => 'integer',
        'status' => 'integer',
    ];

    public function orden_servicio(){
        return $this->belongsTo(OrdenesServicio::class,'orden_servicio_id');
    }
    public function user(){
        return $this->belongsTo(User::class,'user_id');
    }
    public function conceptos(){
        return $this->hasMany(ConceptosPerValeAlmacen::class,'vale_almacen_id');
    }

    public function tipoRegistro()
    {
        return $this->belongsTo(Tipos::class, 'tipo');
    }

    public function estatusRegistro()
    {
        return $this->belongsTo(Estatus::class, 'status');
    }
}
