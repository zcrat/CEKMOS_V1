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
        Schema::create('pagos_presupuestos', function (Blueprint $table) {
            $table->id();
            $table->dateTime('fecha_pagado');
            $table->decimal('importe',10,2);
            $table->foreignId('presupuestos_id')->constrained('presupuestos');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pagos_presupuestos');
    }
};
