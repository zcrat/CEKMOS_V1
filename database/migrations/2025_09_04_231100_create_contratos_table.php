<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contratos', function (Blueprint $table) {
            $table->id();
            $table->string('descripcion');
            $table->string('tipo')->nullable();
            $table->string('numero')->nullable();
            $table->decimal('monto',10,2);
            $table->foreignId('modulo_id')->constrained('modulos');
            $table->foreignId('zona_id')->constrained('zonas');
            $table->year('año');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contratos');
    }
};
