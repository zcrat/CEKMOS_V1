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
        Schema::create('exteriores_orden_servicios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('orden_servicio_id')->constrained('ordenes_servicio');
            $table->foreignId('antena_radio')->constrained('estatus');
            $table->foreignId('antena_telefono')->constrained('estatus');
            $table->foreignId('antena_cb')->constrained('estatus');
            $table->foreignId('estribos')->constrained('estatus');
            $table->foreignId('espejos_laterales')->constrained('estatus');
            $table->foreignId('guardafangos')->constrained('estatus');
            $table->foreignId('parabrisas')->constrained('estatus');
            $table->foreignId('sistema_alarma')->constrained('estatus');
            $table->foreignId('limpia_parabrisas')->constrained('estatus');
            $table->foreignId('luces_exteriores')->constrained('estatus');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exteriores_orden_servicios');
    }
};
