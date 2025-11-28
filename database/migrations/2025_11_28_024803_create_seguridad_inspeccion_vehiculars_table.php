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
        Schema::create('seguridad_inspeccion_vehicular', function (Blueprint $table) {
            $table->id();
            $table->foreignId('frenos_emergencia')->constrained('estatus');
            $table->foreignId('limpiaparabrisas_izquierdo_derecho')->constrained('estatus');
            $table->foreignId('limpiaparabrisas_trasero')->constrained('estatus');
            $table->string('notas');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('seguridad_inspeccion_vehicular');
    }
};
