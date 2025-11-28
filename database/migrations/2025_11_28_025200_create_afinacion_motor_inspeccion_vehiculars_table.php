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
        Schema::create('afinacion_motor_inspeccion_vehicular', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tapa_distribuidor_bujias_cables')->constrained('estatus');
            $table->foreignId('fuel_injection')->constrained('estatus');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('afinacion_motor_inspeccion_vehicular');
    }
};
