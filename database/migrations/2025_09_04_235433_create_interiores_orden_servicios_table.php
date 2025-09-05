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
        Schema::create('interiores_orden_servicios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('orden_servicio_id')->constrained('ordenes_servicio');
            $table->foreignId('puerta_interior_frontal')->constrained('estatus');
            $table->foreignId('puerta_interior_trasera')->constrained('estatus');
            $table->foreignId('puerta_delantera_frontal')->constrained('estatus');
            $table->foreignId('puerta_delantera_trasera')->constrained('estatus');
            $table->foreignId('asiento_interior_frontal')->constrained('estatus');
            $table->foreignId('asiento_interior_trasera')->constrained('estatus');
            $table->foreignId('asiento_delantera_frontal')->constrained('estatus');
            $table->foreignId('asiento_delantera_trasera')->constrained('estatus');
            $table->foreignId('consola_central')->constrained('estatus');
            $table->foreignId('claxon')->constrained('estatus');
            $table->foreignId('tablero')->constrained('estatus');
            $table->foreignId('quemacocos')->constrained('estatus');
            $table->foreignId('toldo')->constrained('estatus');
            $table->foreignId('elevadores_eletricos')->constrained('estatus');
            $table->foreignId('luces_interiores')->constrained('estatus');
            $table->foreignId('seguros_eletricos')->constrained('estatus');
            $table->foreignId('tapetes')->constrained('estatus');
            $table->foreignId('climatizador')->constrained('estatus');
            $table->foreignId('radio')->constrained('estatus');
            $table->foreignId('espejos_retrovizor')->constrained('estatus');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('interiores_orden_servicios');
    }
};
