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
        Schema::create('inspeccion_vehicular', function (Blueprint $table) {
            $table->id();
            $table->foreignId('orden_servicio_id')->constrained('unidades_sat');
            $table->foreignId('llantas_id')->constrained('llantas_inspeccion_vehicular');
            $table->foreignId('liquidos_id')->constrained('liquidos_inspeccion_vehicular');
            $table->foreignId('bandas_id')->constrained('bandas_inspeccion_vehicular');
            $table->foreignId('seguridad_id')->constrained('seguridad_inspeccion_vehicular');
            $table->foreignId('filtros_id')->constrained('filtros_inspeccion_vehicular');
            $table->foreignId('escape_id')->constrained('escape_inspeccion_vehicular');
            $table->foreignId('suspencion_direccion_id')->constrained('suspencion_direccion_inspeccion_vehicular');
            $table->foreignId('afinacion_motor_id')->constrained('afinacion_motor_inspeccion_vehicular');
            $table->foreignId('tren_transmision_id')->constrained('tren_transmision_inspeccion_vehicular');
            $table->foreignId('frenos_id')->constrained('frenos_inspeccion_vehicular');
            $table->foreignId('electrico_id')->constrained('electrico_inspeccion_vehicular');
            $table->foreignId('revision_luces_espias_id')->constrained('revision_luces_espias_inspeccion_vehicular');
            $table->foreignId('mangueras_id')->constrained('mangueras_inspeccion_vehicular');
            $table->foreignId('user_id')->constrained('user_inspeccion_vehicular');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inspeccion_vehicular');
    }
};
