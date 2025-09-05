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
        Schema::create('modulo_ordenes_servicios', function (Blueprint $table) {
            $table->id();
            $table->string('descripcion');
            $table->string('clave');
            $table->foreignId('modulo_id')->constrained('modulos');
            $table->foreignId('contrato_id')->constrained('contratos');
            $table->foreignId('zona_id')->constrained('zonas');
            $table->year('año');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('modulo_ordenes_servicios');
    }
};
