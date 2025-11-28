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
        Schema::create('conceptos_per_presupuestos', function (Blueprint $table) {
            $table->id();
            $table->decimal('cantidad',10,2);
            $table->decimal('costo',10,2);
            $table->decimal('venta',10,2);
            $table->foreignId('presupuesto_id')->constrained('presupuestos');
            $table->foreignId('concepto_presupuesto_id')->constrained('conceptos_presupuestos');
            $table->foreignId('user_id')->constrained('users');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('conceptos_per_presupuestos');
    }
};
