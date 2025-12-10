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
        Schema::create('hoja_conceptos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('orden_servicio_id')->constrained('ordenes_servicio');
            $table->foreignId('concepto_id')->constrained('conceptos_almacen');
            $table->foreignId('tipo_id')->constrained('tipos');
            $table->boolean('new');
            $table->decimal('cantidad',8,2);
            $table->decimal('costo',8,2);
            $table->decimal('venta',8,2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hoja_conceptos');
    }
};
