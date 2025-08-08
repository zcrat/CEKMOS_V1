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
            $table->string('clave', 3)->primary(); // clave es la PK, ya no es necesario unique() también
            $table->string('descripcion');
            $table->boolean('persona_fisica')->default(false);
            $table->boolean('persona_moral')->default(false);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('employes', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('paterno');
            $table->string('materno');
            $table->string('curp', 18);
            $table->string('rfc', 13);
            $table->string('regimen_fiscal_id', 3); // ⚠️ importante que coincida con el tipo
            $table->foreign('regimen_fiscal_id')->references('clave')->on('regimes_fiscales');
            $table->integer('domicilio_fiscal');
            $table->timestamps();
            $table->softDeletes();
        });

    }
    public function down(): void
    {
          Schema::dropIfExists('regimes_fiscales');
          Schema::dropIfExists('employes');
    }
};
