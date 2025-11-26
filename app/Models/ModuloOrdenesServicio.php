<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class ModuloOrdenesServicio extends Model
{       
    use SoftDeletes;
    protected $table = 'modulo_ordenes_servicios';
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
        return $this->belongsTo(Modulos::class, 'modulo_id');
    }
    public function zona()
    {
        return $this->belongsTo(Zonas::class, 'zona_id');
    }
    public function contrato()
    {
        return $this->belongsTo(Contratos::class, 'contrato_id');
    }
    public function ordenes_servicio()
    {
        return $this->hasMany(OrdenesServicio::class, 'modulo_orden_id');
    }
    public function vehiculos_conceptos(){
        return $this->hasMany(VehiculosConceptosDisponibles::class, 'modulo_orden_id');
    }
    public function conceptos_presupuestos(){
        return $this->hasMany(ConceptosPresupuestos::class,'modulo_orden_servicio_id');
    }
}
