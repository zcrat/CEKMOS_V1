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
        Schema::create('bandas_inspeccion_vehicular', function (Blueprint $table) {
            $table->id();
            $table->foreignId('accesorios')->constrained('estatus');
            $table->foreignId('bandas_direccion_hidraulica')->constrained('estatus');
            $table->foreignId('alternador_aire_acondicionado')->constrained('estatus');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bandas_inspeccion_vehicular');
    }
};
