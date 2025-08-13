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
        Schema::create('cfdis', function (Blueprint $table) {
            $table->id();
            $table->string('usocfdi');
            $table->string('descripcion');
            $table->boolean('persona_fisica');
            $table->boolean('persona_moral');
            $table->timestamps();
            $table->timestamp('deleted_at')->nullable();
        });
        Schema::create('cfdis_avaliables', function (Blueprint $table) {
            $table->id();
            $table->string('cfdi_id');
            $table->string('regimen_fiscal');
            $table->timestamps();
            $table->timestamp('deleted_at')->nullable();
        });


    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cfdis');
        Schema::dropIfExists('cfdis_avaliables');
    }
};
