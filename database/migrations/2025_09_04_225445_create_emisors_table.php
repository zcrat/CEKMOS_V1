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
        Schema::create('emisores', function (Blueprint $table) {
            $table->id();
            $table->string('n_certificado');
            $table->string('archivo_cer');
            $table->string('archivo_key');
            $table->string('clave_key');
            $table->string('rfc');
            $table->string('nombre');
            $table->string('logotipo');
            $table->string('regimen');
            $table->string('codigo');
            $table->string('serie_factura');
            $table->string('serie_p_factura');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('emisors');
    }
};
