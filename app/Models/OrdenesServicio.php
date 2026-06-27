<?php

namespace App\Models;

use Carbon\Traits\Timestamp;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class OrdenesServicio extends Model
{
    use SoftDeletes;
    protected $table = 'ordenes_servicio';
    protected $fillable = [
        'orden_servicio',
        'orden_seguimiento',
        'orden_opcional',
        'modulo_orden_id',
        'vehiculo_id',
        'vehiculo_concepto_id',
        'user_id',
        'empresa_id',
        'cliente_id',
        'estimacion',
        'estatus_id',
        'fallas_reportadas',
        'notas_retraso',
        'telefono',
        'ubicacion_id',
    ];
    protected $casts = [
        'estimacion' => 'datetime'
    ];

    public function archivos(){
        return $this->hasManyThrough(
            Archivos::class,
            RecepcionesVehiculares::class,
            'orden_servicio_id',
            'recepcion_vehicular_id',
            'id',
            'id'
        );
    }
    public function modulo_ordenes_servicio()
    {
        return $this->belongsTo(ModuloOrdenesServicio::class, 'modulo_orden_id');
    }
    public function vehiculo()
    {
        return $this->belongsTo(Vehiculos::class, 'vehiculo_id');
    }
    public function vehiculo_concepto()
    {
        return $this->belongsTo(VehiculosConceptos::class, 'vehiculo_concepto_id');
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function empresa()
    {
        return $this->belongsTo(Empresas::class, 'empresa_id');
    }
    public function cliente()
    {
        return $this->belongsTo(Clientes::class, 'cliente_id');
    }
    public function entrada(){
        return $this->hasOne(DatosEntrada::class,'orden_servicio_id');
    }
    public function salida(){
        return $this->hasOne(DatosSalida::class,'orden_servicio_id');
    }
    public function presupuestos(){
        return $this->hasMany(Presupuestos::class,'orden_servicio_id');
    }
    public function responsables()
    {
        return $this->hasOne(ResponsablesOrdenServicio::class,'orden_servicio_id');
    }
    public function recepcion_vehicular()
    {
        return $this->hasOne(RecepcionesVehiculares::class,'orden_servicio_id');
    }
    public function interiores()
    {
        return $this->hasOneThrough(
            InterioresOrdenServicio::class,
            RecepcionesVehiculares::class,
            'orden_servicio_id',
            'recepcion_vehicular_id',
            'id',
            'id'
        );
    }
    public function exteriores()
    {
        return $this->hasOneThrough(
            ExterioresOrdenServicio::class,
            RecepcionesVehiculares::class,
            'orden_servicio_id',
            'recepcion_vehicular_id',
            'id',
            'id'
        );
    }
    public function inventario()
    {
        return $this->hasOneThrough(
            InventarioOrdenServicio::class,
            RecepcionesVehiculares::class,
            'orden_servicio_id',
            'recepcion_vehicular_id',
            'id',
            'id'
        );
    }
    public function condiciones_pintura()
    {
        return $this->hasOneThrough(
            CondicionesPinturaOrdenServicio::class,
            RecepcionesVehiculares::class,
            'orden_servicio_id',
            'recepcion_vehicular_id',
            'id',
            'id'
        );
    }
    public function pedidos_almacen(){
        return $this->hasMany(PedidosOrdenesAlmacen::class,'orden_servicio_id');
    }
    public function log_acciones(){
        return $this->hasMany(LogAccionesOS::class,'orden_servicio_id');
    }
    public function vales_almacen(){
        return $this->hasMany(ValesAlmacen::class,'orden_servicio_id');
    }
    public function hoja_conceptos(){
        return $this->hasMany(HojaConceptos::class,'orden_servicio_id');
    }
    public function ubicacion(){
        return $this->belongsTo(Ubicaciones::class,'ubicacion_id');
    }
    public function seguimiento(){
        return $this->hasMany(SeguimientoUnidades::class,'orden_servicio_id');
    }
    public function last_seguimiento(){
        return $this->hasOne(SeguimientoUnidades::class,'orden_servicio_id')->orderbydesc('id');
    }
}
