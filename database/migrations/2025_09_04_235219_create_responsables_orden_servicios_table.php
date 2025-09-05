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
        Schema::create('responsables_orden_servicios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('administrador_transporte_id')->constrained('usuarios_taller');
            $table->foreignId('jefe_de_proceso_id')->constrained('usuarios_taller');
            $table->foreignId('trabajador_id')->constrained('usuarios_taller');
            $table->foreignId('tecnico_id')->constrained('usuarios_taller');
            $table->foreignId('orden_servicio_id')->constrained('ordenes_servicio');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('responsables_orden_servicios');
    }
};
