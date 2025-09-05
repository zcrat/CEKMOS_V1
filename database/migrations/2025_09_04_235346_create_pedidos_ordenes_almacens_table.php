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
        Schema::create('pedidos_ordenes_almacens', function (Blueprint $table) {
            $table->id();
            $table->dateTime('pedido_hecho');
                $table->dateTime('pedido_entregado')->nullable();
                $table->foreignId('orden_servicio_id')->constrained('ordenes_servicio');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pedidos_ordenes_almacens');
    }
};
