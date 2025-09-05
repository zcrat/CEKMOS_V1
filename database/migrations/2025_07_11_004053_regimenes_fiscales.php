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
            $table->string('clave', 3)->primary();
            $table->string('descripcion');
            $table->boolean('persona_fisica')->default(false);
            $table->boolean('persona_moral')->default(false);
            $table->timestamps();
            $table->softDeletes();
        });
    }
    public function down(): void
    {
          Schema::dropIfExists('regimes_fiscales');
    }
};
