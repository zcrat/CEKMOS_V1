<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class OrdenesServicio extends Model
{
    use SoftDeletes;
    protected $table = 'ordenes_servicio';
    protected $fillable = [
        'orden_servicio',
        'orden_seguimiento',
        'modulo_orden_id',
        'vehiculo_id',
        'vehiculo_concepto_id',
        'user_id',
        'empresa_id',
        'cliente_id',
        'update_fotos',
        'diagnostico',
        'indicaciones_cliente',
        'notas_mecanico',
        'notas_retraso',
        'telefono',
        'ubicacion',
    ];
    protected $casts = [
        'update_fotos' => 'boolean',
        'diagnostico' => 'datetime',
    ];

    public function archivos(){
        return $this->hasMany(Archivos::class,'orden_servicio_id');
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
    public function interiores()
    {
        return $this->hasOne(InterioresOrdenServicio::class,'orden_servicio_id');
    }
    public function exteriores()
    {
        return $this->hasOne(ExterioresOrdenServicio::class,'orden_servicio_id');
    }
    public function inventario()
    {
        return $this->hasOne(InventarioOrdenServicio::class,'orden_servicio_id');
    }
    public function condiciones_pintura()
    {
        return $this->hasOne(CondicionesPinturaOrdenServicio ::class,'orden_servicio_id');
    }
    public function pedidos_almacen(){
        return $this->hasMany(PedidosOrdenesAlmacen::class,'orden_servicio_id');
    }
    public function log_acciones(){
        return $this->hasMany(LogAccionesOS::class,'orden_servicio_id');
    }

}
