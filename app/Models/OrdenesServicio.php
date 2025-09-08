<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class OrdenesServicio extends Model
{
    use SoftDeletes;
    protected $table = 'ordenes_servicios';
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

    public function ModuloOrden()
    {
        return $this->belongsTo(ModuloOrdenesServicio::class, 'modulo_orden_id');
    }
    public function Vehiculo()
    {
        return $this->belongsTo(Vehiculos::class, 'vehiculo_id');
    }
    public function Vehiculo_Concepto()
    {
        return $this->belongsTo(VehiculosConceptos::class, 'tipo_vehiculo_concepto_id');
    }
    public function User()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function Empresa()
    {
        return $this->belongsTo(Empresas::class, 'empresa_id');
    }
    public function Cliente()
    {
        return $this->belongsTo(Clientes::class, 'cliente_id');
    }
    public function Archivos(){
        return $this->hasMany(ArchivosOrdenServicio::class,'orden_servicio_id');
    }
    public function Entrada(){
        return $this->hasOne(DatosEntrada::class,'orden_servicio_id');
    }
    public function Salida(){
        return $this->hasOne(DatosSalida::class,'orden_servicio_id');
    }
    public function Presupuestos(){
        return $this->hasMany(Presupuestos::class,'orden_servicio_id');
    }
    public function Responsables()
    {
        return $this->hasOne(ResponsablesOrdenServicio::class,'orden_servicio_id');
    }
    public function Interiores()
    {
        return $this->hasOne(InterioresOrdenServicio::class,'orden_servicio_id');
    }
    public function Exteriores()
    {
        return $this->hasOne(ExterioresOrdenServicio::class,'orden_servicio_id');
    }
    public function Inventarios()
    {
        return $this->hasOne(InventarioOrdenServicio::class,'orden_servicio_id');
    }
    public function CondicionesPintura()
    {
        return $this->hasOne(CondicionesPinturaOrdenServicio ::class,'orden_servicio_id');
    }
    public function PedidosAlmacen(){
        return $this->hasMany(PedidosOrdenesAlmacen::class,'orden_servicio_id');
    }


}
