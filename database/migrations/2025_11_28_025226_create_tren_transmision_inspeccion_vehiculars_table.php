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
        Schema::create('tren_transmision_inspeccion_vehicular', function (Blueprint $table) {
            $table->id();
            $table->foreignId('filtro_transmison')->constrained('estatus');
            $table->foreignId('union_transmision_clutch')->constrained('estatus');
            $table->foreignId('eje_traccion_juntas_homocineticas')->constrained('estatus');
            $table->foreignId('eje_transmision_juntas_universales')->constrained('estatus');
            $table->foreignId('rodamientos_rueda')->constrained('estatus');
            $table->foreignId('tren_transmision')->constrained('estatus');
            $table->foreignId('clutch')->constrained('estatus');
            $table->string('notas');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tren_transmision_inspeccion_vehicular');
    }
};
