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
        Schema::create('archivos_orden_servicios', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->foreignId('orden_servicio_id')->constrained('ordenes_servicio');
            $table->foreignId('tipo_id')->constrained('tipos');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('archivos_orden_servicios');
    }
};
