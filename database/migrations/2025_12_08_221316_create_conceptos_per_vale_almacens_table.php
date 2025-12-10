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
        Schema::create('conceptos_per_vale_almacen', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vale_almacen_id')->constrained('vales_almacen');
            $table->foreignId('concepto_id')->constrained('conceptos_almacen');
            $table->decimal('cantidad',8,2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('conceptos_per_vale_almacen');
    }
};
