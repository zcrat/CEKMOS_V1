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
        Schema::create('log_acciones', function (Blueprint $table) {
            $table->id();
            $table->text('que_hizo');
            $table->foreignId('orden_servicio_id')->constrained('ordenes_servicio');
            $table->foreignId('presupuesto_id')->constrained('presupuestos')->nullable();
            $table->foreignId('tipo_id')->constrained('tipos');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('log_acciones');
    }
};      
