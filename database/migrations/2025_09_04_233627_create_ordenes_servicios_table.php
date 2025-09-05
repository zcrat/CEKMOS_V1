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
    {
        Schema::create('ordenes_servicios', function (Blueprint $table) {
            $table->id();
            $table->string('orden_servicio')->unique();
            $table->string('orden_seguimiento')->unique();
            $table->foreignId('modulo_orden')->constrained('modulos_ordenes_servicio');
            $table->foreignId('vehiculo_id')->constrained('vehiculos');
            $table->foreignId('tipo_vehiculo_concepto_id')->constrained('vehiculos_conceptos');
            $table->foreignId('user_id')->constrained('users');  
            $table->foreignId('empresa_id')->constrained('empresas');
            $table->foreignId('cliente_id')->constrained('clientes');
            $table->boolean('update_fotos')->default(false);
            $table->dateTime('diagnostico')->nullable();
            $table->text('indicaciones_cliente')->nullable();
            $table->text('notas_mecanico')->nullable();
            $table->text('notas_retraso')->nullable();
            $table->string('telefono')->nullable();
            $table->string('ubicacion')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ordenes_servicios');
    }
};
