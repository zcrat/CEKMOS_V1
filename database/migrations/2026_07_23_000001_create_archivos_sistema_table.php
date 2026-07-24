<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('archivos_sistema', function (Blueprint $table) {
            $table->id();
            $table->string('nombre_archivo');
            $table->string('tipo_archivo', 50);
            $table->foreignId('usuario_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();
            $table->string('estatus_resultante', 50)->default('procesando');
            $table->json('datos_entrada')->nullable();
            $table->timestamps();
        });

        Schema::table('conceptos_presupuestos', function (Blueprint $table) {
            $table->foreignId('archivo_sistema_id')
                ->nullable()
                ->after('unidad_sat_id')
                ->constrained('archivos_sistema')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('conceptos_presupuestos', function (Blueprint $table) {
            $table->dropConstrainedForeignId('archivo_sistema_id');
        });

        Schema::dropIfExists('archivos_sistema');
    }
};
