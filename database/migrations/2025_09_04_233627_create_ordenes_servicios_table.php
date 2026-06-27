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
        Schema::create('ordenes_servicio', function (Blueprint $table) {
            $table->id();
            $table->string('orden_servicio')->unique();
            $table->string('orden_seguimiento')->unique();
            $table->string('orden_opcional')->nullable();
            $table->foreignId('modulo_orden_id')->constrained('modulo_ordenes_servicios');
            $table->foreignId('ubicacion_id')->constrained('Ubicaciones');
            $table->foreignId('vehiculo_id')->constrained('vehiculos');
            $table->foreignId('vehiculo_concepto_id')->constrained('vehiculos_conceptos');
            $table->foreignId('user_id')->constrained('users');  
            $table->foreignId('empresa_id')->constrained('empresas');
            $table->foreignId('cliente_id')->constrained('clientes');
            $table->dateTime('diagnostico')->nullable();
            $table->text('fallas_reportadas')->nullable();
            $table->text('notas_retraso')->nullable();
            $table->string('telefono')->nullable();
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
