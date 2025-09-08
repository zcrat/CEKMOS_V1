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
        Schema::create('datos_entradas', function (Blueprint $table) {
            $table->id();
            $table->dateTime('fecha');
            $table->dateTime('estimacion');
            $table->decimal('kilomentraje',10,2);
            $table->foreignId('gasolina')->constrained('niveles_combustible');
            $table->foreignId('orden_servicio_id')->constrained('ordenes_servicio');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('datos_entradas');
    }
};
