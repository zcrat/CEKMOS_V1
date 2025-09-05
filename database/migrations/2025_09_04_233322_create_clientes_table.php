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
        Schema::create('clientes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empresa_id')->constrained('empresas');
            $table->string('nombre');
            $table->string('rfc');
            $table->string('email');
            $table->string('logo');
            $table->string('calle');
            $table->integer('cp');
            $table->foreignId('ciudad_id')->constrained('ciudades');
            $table->foreignId('emisor_id')->constrained('emisor');
            $table->integer('telefono');
            $table->integer('tel_celular');
            $table->integer('tel_negocio');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clientes');
    }
};
