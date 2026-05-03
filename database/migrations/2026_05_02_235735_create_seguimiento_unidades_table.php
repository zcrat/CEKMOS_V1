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
        Schema::create('seguimiento_unidades', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ordene_servicio_id')->constrained('ordenes_servicio');
            $table->foreignId('tipo_id')->constrained('tipos');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('seguimiento_unidades');
    }
};
