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
        Schema::create('vehiculos', function (Blueprint $table) {
            $table->id();
            $table->string('placas'); 
            $table->year('año'); 
            $table->string('economomico'); 
            $table->string('vin'); 
            $table->foreignId('tipo_id')->constrained('tipos');
            $table->foreignId('color_id')->constrained('colores');
            $table->foreignId('modelo_id')->constrained('modelos');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehiculos');
    }
};
