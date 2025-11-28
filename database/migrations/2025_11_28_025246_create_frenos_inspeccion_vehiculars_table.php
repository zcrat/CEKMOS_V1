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
        Schema::create('frenos_inspeccion_vehicular', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pastillas_izquierda_delantera')->constrained('estatus');
            $table->foreignId('pastillas_izquierda_trasera')->constrained('estatus');
            $table->foreignId('pastillas_derecha_delantera')->constrained('estatus');
            $table->foreignId('pastillas_derecha_trasera')->constrained('estatus');
            $table->foreignId('rotores_izquierda_delantera')->constrained('estatus');
            $table->foreignId('rotores_izquierda_trasera')->constrained('estatus');
            $table->foreignId('rotores_derecha_delantera	')->constrained('estatus');
            $table->foreignId('rotores_derecha_trasera')->constrained('estatus');
            $table->foreignId('pinzas_cilindros_rueda_izquierda_delantera')->constrained('estatus');
            $table->foreignId('pinzas_cilindros_rueda_izquierda_trasera')->constrained('estatus');
            $table->foreignId('pinzas_cilindros_rueda_derecha_delantera')->constrained('estatus');
            $table->foreignId('pinzas_cilindros_rueda_derecha_trasera')->constrained('estatus');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('frenos_inspeccion_vehicular');
    }
};
