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
        Schema::create('electrico_inspeccion_vehicular', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sistema_carga_bateria')->constrained('estatus');
            $table->foreignId('cables_conexiones_fusibles')->constrained('estatus');
            $table->foreignId('faro_izquierda')->constrained('estatus');
            $table->foreignId('faro_derecha')->constrained('estatus');
            $table->foreignId('cuarto_izquierda')->constrained('estatus');
            $table->foreignId('cuarto_derecha')->constrained('estatus');
            $table->foreignId('reversa_frenos')->constrained('estatus');
            $table->foreignId('direccionales_izquierda_delantera')->constrained('estatus');
            $table->foreignId('direccionales_derecha_delantera')->constrained('estatus');
            $table->foreignId('direccionales_izquierda_trasera')->constrained('estatus');
            $table->foreignId('direccionales_derecha_trasera')->constrained('estatus');
            $table->foreignId('intermitentes')->constrained('estatus');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('electrico_inspeccion_vehicular');
    }
};
