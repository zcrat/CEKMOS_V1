<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {'', '', '', '',
        'Kilometraje_entrada', 'Gas_entrada', '',
        'Kilometraje_salida', 'Gas_salida', '','Diagnostico','PedidoHecho','PedidoEntregado','Vehiculo_id','Tipo_Vehiculo_Concepto_id',
        'User_id','User_update_id', 'Empresa_id', 'Customer_id', 'AdministradorTrasporte_id',
        'JefedeProceso_id', 'Trabajador_id', 'Telefono', 'contrato_id',
        'modulo_id', 'anio', 'zona_id','Indicaciones_cliente'
    Schema::create('niveles_combustible', function (Blueprint $table) {
        $table->id();
        $table->string('descripcion'); 
        $table->timestamps();
    });
    Schema::create('datos_entrada', function (Blueprint $table) {
        $table->id();
        $table->dateTime('fecha');
        $table->integer('kilomentraje');
        $table->foreignId('gasolina')->constrained('niveles_combustible');
        $table->foreignId('orden_servicio_id')->constrained('ordenes_servicio');
    });
    Schema::create('datos_salida', function (Blueprint $table) {
        $table->id();
        $table->dateTime('fecha');
        $table->integer('kilomentraje');
        $table->foreignId('gasolina')->constrained('niveles_combustible');
        $table->foreignId('orden_servicio_id')->constrained('ordenes_servicio');
    });
    Schema::create('datos_pedido_almacen', function (Blueprint $table) {
        $table->id();
        $table->dateTime('fecha');
        $table->integer('kilomentraje');
        $table->foreignId('gasolina')->constrained('niveles_combustible');
        $table->foreignId('orden_servicio_id')->constrained('ordenes_servicio');
    });

    Schema::create('responsables_orden_servicio', function (Blueprint $table) {
        $table->id();
        $table->foreignId('administrador_transporte_id')->constrained('users');
        $table->foreignId('customer_id')->constrained('customers');
        $table->foreignId('jefe_de_proceso_id')->constrained('users');
        $table->foreignId('trabajador_id')->constrained('users');
        $table->foreignId('tecnico_id')->constrained('empresas');
        $table->foreignId('orden_servicio_id')->constrained('ordenes_servicio');
    });

    Schema::create('modulos_ordenes_servicio', function (Blueprint $table) {
        $table->id();
        $table->foreignId('contrato_id')->constrained('contratos');
        $table->foreignId('modulo_id')->constrained('modulos');
        $table->foreignId('zona_id')->constrained('zonas');
        $table->year('anio');
    });





    Schema::create('ordenes_servicio', function (Blueprint $table) {
        $table->id();
        $table->string('orden_servicio')->unique();
        $table->string('orden_seguimiento')->unique();
        $table->foreignId('modulo_orden')->constrained('modulos_ordenes_servicio');
        $table->foreignId('vehiculo_id')->constrained('vehiculos');
        $table->foreignId('tipo_vehiculo_concepto_id')->constrained('tipos_vehiculo');
        $table->foreignId('user_id')->constrained('users');  
        $table->foreignId('empresa_id')->constrained('empresas');
        $table->foreignId('cliente_id')->constrained('empresas');
    
        // Información de diagnóstico
        $table->text('diagnostico')->nullable();
        $table->text('firma')->nullable();
        $table->text('carro')->nullable();
        $table->boolean('pedido_hecho')->default(false);
        $table->boolean('pedido_entregado')->default(false);
        $table->boolean('update_fotos')->default(false);
        
        
        $table->string('ubicacion')->nullable();
        $table->dateTime('fecha_esperada');
        $table->text('indicaciones_cliente')->nullable();
        $table->text('notas_mecanico')->nullable();
        $table->text('notas_retraso')->nullable();
        $table->text('indicaciones_cliente')->nullable();
        $table->string('telefono')->nullable();

    $table->timestamps();
});

    }
    public function down(): void
    {
         Schema::dropIfExists('fondos_sustituidos');
    }
};
