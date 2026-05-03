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
        Schema::create('archivos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->foreignId('orden_servicio_id')->constrained('ordenes_servicio');
            $table->unsignedBigInteger('presupuesto_id')->nullable();
            $table->foreign('presupuesto_id')->references('id')->on('presupuestos');
            $table->foreignId('tipo_id')->constrained('tipos');
            $table->foreignId('estatus_id')->constrained('estatus');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('archivos');
    }
};      
