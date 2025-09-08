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
        Schema::create('cfdis_disponibles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('uso_cfdi_id')->references('id')->on('cfdis');
            $table->string('regimen_fiscal_id', 3); // ⚠️ importante que coincida con el tipo
            $table->foreign('regimen_fiscal_id')->references('clave')->on('regimes_fiscales');
            $table->timestamps();
            $table->softDeletes();
        });


    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cfdis_disponibles');
    }
};
