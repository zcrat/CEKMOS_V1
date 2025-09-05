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
        Schema::create('vehiculos_conceptos_disponibles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tipo_id')->constrained('vehiculos_conceptos');
            $table->foreignId('modulo_orden')->constrained('modulos_ordenes_servicio');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehiculos_conceptos_disponibles');
    }
};
