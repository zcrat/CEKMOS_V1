<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('archivos_sistema', function (Blueprint $table) {
            $table->string('disco', 50)->default('local')->after('tipo_archivo');
            $table->string('ruta_archivo')->nullable()->after('disco');
            $table->string('ruta_resultado')->nullable()->after('ruta_archivo');
            $table->json('detalles_procesamiento')->nullable()->after('estatus_resultante');
        });
    }

    public function down(): void
    {
        Schema::table('archivos_sistema', function (Blueprint $table) {
            $table->dropColumn([
                'disco',
                'ruta_archivo',
                'ruta_resultado',
                'detalles_procesamiento',
            ]);
        });
    }
};
