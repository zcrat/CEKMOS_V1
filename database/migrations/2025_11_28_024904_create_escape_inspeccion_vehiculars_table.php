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
        Schema::create('escape_inspeccion_vehicular', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mofle_convertidor_catlitico')->constrained('estatus');
            $table->foreignId('sensores_soporte_tubos')->constrained('estatus');
            $table->string('notas');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('escape_inspeccion_vehicular');
    }
};
