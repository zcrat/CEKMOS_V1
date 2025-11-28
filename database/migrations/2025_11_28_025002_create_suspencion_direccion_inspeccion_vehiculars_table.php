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
        Schema::create('suspencion_direccion_inspeccion_vehicular', function (Blueprint $table) {
            $table->id();
            $table->foreignId('amortiguadores_suspencion')->constrained('estatus');
            $table->foreignId('juntas_direccion_rotulas')->constrained('estatus');
            $table->string('notas');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('suspencion_direccion_inspeccion_vehicular');
    }
};
