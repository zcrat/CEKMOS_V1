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
        Schema::create('liquidos_inspeccion_vehicular', function (Blueprint $table) {
            $table->id();
            $table->foreignId('alternador_aire_acondicionado')->constrained('estatus');
            $table->boolean('alternador_aire_acondicionado_ok');
            $table->boolean('alternador_aire_acondicionado_lleno');
            $table->foreignId('transmision')->constrained('estatus');
            $table->boolean('transmision_ok');
            $table->boolean('transmision_lleno');
            $table->foreignId('diferencial_frente_trasero')->constrained('estatus');
            $table->boolean('diferencial_frente_trasero_ok');
            $table->boolean('diferencial_frente_trasero_lleno');
            $table->foreignId('refrigerante')->constrained('estatus');
            $table->boolean('refrigerante_ok');
            $table->boolean('refrigerante_lleno');
            $table->foreignId('frenos')->constrained('estatus');
            $table->boolean('frenos_ok');
            $table->boolean('frenos_lleno');
            $table->foreignId('direccion_hidraulica')->constrained('estatus');
            $table->boolean('direccion_hidraulica_ok');
            $table->boolean('direccion_hidraulica_lleno');
            $table->foreignId('limpiaparabrisas')->constrained('estatus');
            $table->boolean('limpiaparabrisas_ok');
            $table->boolean('limpiaparabrisas_lleno');
            $table->string('notas');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('liquidos_inspeccion_vehicular');
    }
};
