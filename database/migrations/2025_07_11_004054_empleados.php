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
        Schema::create('regimes_fiscales', function (Blueprint $table) {
            $table->id();
            $table->integer('clave');
            $table->string('descripcion');
            $table->string('regimen_fiscal');
            $table->timestamps();
            $table->timestamp('deleted_at')->nullable();
        });
        Schema::create('employes', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('paterno');
            $table->string('materno');
            $table->string('curp',18);
            $table->string('rfc',13);
            $table->foreignId('regimen_fiscal_id')->references('id')->on('regimes_fiscales');
            $table->string('uso_cfdi',6);
            $table->integer('domicilio_fiscal');
            $table->timestamps();
            $table->timestamp('deleted_at')->nullable();
        });
    }
    public function down(): void
    {
          Schema::dropIfExists('regimes_fiscales');
          Schema::dropIfExists('employes');
    }
};
