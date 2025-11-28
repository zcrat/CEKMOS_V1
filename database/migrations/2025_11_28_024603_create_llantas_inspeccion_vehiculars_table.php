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
        Schema::create('llantas_inspeccion_vehicular', function (Blueprint $table) {
            $table->id();
            $table->foreignId('izquierda_delantera')->constrained('estatus');
            $table->decimal('izquierda_delantera_presion',10,2);
            $table->foreignId('izquierda_trasera')->constrained('estatus');
            $table->decimal('izquierda_trasera_presion',10,2);
            $table->foreignId('derecha_delantera')->constrained('estatus');
            $table->decimal('derecha_delantera_presion',10,2);
            $table->foreignId('derecha_trasera')->constrained('estatus');
            $table->decimal('derecha_trasera_presion',10,2);
            $table->foreignId('refaccion')->constrained('estatus');
            $table->decimal('refaccion_presion',10,2);
            $table->foreignId('alineacion_balanceo')->constrained('estatus');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('llantas_inspeccion_vehicular');
    }
};
