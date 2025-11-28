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
        Schema::create('mangueras_inspeccion_vehicular', function (Blueprint $table) {
            $table->id();
            $table->foreignId('refrigerante')->constrained('estatus');
            $table->foreignId('direccion_aire_acondicionado')->constrained('estatus');
            $table->foreignId('calefaccion')->constrained('estatus');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mangueras_inspeccion_vehicular');
    }
};
