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
        Schema::create('motores', function (Blueprint $table) {
            $table->id();
            $table->string('descripcion')->unique();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('modelos', function (Blueprint $table) {
            $table->id();
            $table->string('descripcion');
            $table->foreignId('marca_id')->constrained('marcas');
            $table->foreignId('motor_id')->constrained('motores');
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['marca_id', 'descripcion', 'motor_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('modelos');
        Schema::dropIfExists('motores');
    }
};
