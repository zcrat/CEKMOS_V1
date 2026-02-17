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
        Schema::create('empleados', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('paterno');
            $table->string('materno');
            $table->string('curp', 18)->unique();
            $table->string('rfc', 13)->unique();;
            $table->string('regimen_fiscal_id', 3); // ⚠️ importante que coincida con el tipo
            $table->foreign('regimen_fiscal_id')->references('clave')->on('regimes_fiscales');
            $table->string('domicilio_fiscal');
            $table->timestamps();
            $table->softDeletes();
        });

    }
    public function down(): void
    {
          Schema::dropIfExists('empleados');
    }
};
