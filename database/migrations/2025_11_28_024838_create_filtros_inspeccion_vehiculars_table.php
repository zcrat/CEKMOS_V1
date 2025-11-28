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
        Schema::create('filtros_inspeccion_vehicular', function (Blueprint $table) {
            $table->id();
            $table->foreignId('aire')->constrained('estatus');
            $table->foreignId('combustible')->constrained('estatus');
            $table->foreignId('aceite')->constrained('estatus');
            $table->string('notas');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('filtros_inspeccion_vehicular');
    }
};
