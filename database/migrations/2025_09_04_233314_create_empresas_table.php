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
        Schema::create('empresas', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('rfc');
            $table->string('email');
            $table->string('logo');
            $table->string('calle');
            $table->integer('cp');
            $table->foreignId('municipio_id')->constrained('municipios');
            $table->foreignId('user_id')->constrained('users');
            $table->string('regimen_fiscal_id', 3);
            $table->foreign('regimen_fiscal_id')->references('clave')->on('regimes_fiscales');
            $table->string('telefono', 15);
            $table->string('tel_celular', 15);
            $table->string('tel_negocio', 15);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('empresas');
    }
};
