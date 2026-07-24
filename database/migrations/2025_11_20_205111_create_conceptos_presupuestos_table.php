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
        Schema::create('conceptos_presupuestos', function (Blueprint $table) {
            $table->id();
            $table->string('num');
            $table->text('descripcion');
            $table->unsignedInteger('garantia_dias')->nullable();
            $table->boolean('fijo')->default(false);
            $table->foreignId('tipo_id')->constrained('tipos');
            $table->foreignId('modulo_orden_servicio_id')->constrained('modulo_ordenes_servicios');
            $table->foreignId('categoria_sat_id')->constrained('categorias_sat');
            $table->foreignId('unidad_sat_id')->constrained('unidades_sat');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('conceptos_presupuestos');
    }
};
