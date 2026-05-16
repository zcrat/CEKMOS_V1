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
        Schema::table('ordenes_servicio', function (Blueprint $table) {
            $table->renameColumn('update_fotos', 'cambiar_archivos');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
         Schema::table('ordenes_servicio', function (Blueprint $table) {
            $table->renameColumn('cambiar_archivos', 'update_fotos');
        });
    }
};
