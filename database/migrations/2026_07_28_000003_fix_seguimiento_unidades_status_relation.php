<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('seguimiento_unidades', function (Blueprint $table) {
            $table->dropForeign(['tipo_id']);
        });

        Schema::table('seguimiento_unidades', function (Blueprint $table) {
            $table->renameColumn('tipo_id', 'estatus_id');
        });

        $diagnosticoIniciado = DB::table('estatus')
            ->where('categoria_id', 12)
            ->where('descripcion', 'Diagnostico Iniciado')
            ->value('id');
        $diagnosticoTerminado = DB::table('estatus')
            ->where('categoria_id', 12)
            ->where('descripcion', 'Diagnostico Terminado')
            ->value('id');

        if ($diagnosticoIniciado) {
            DB::table('seguimiento_unidades')
                ->where('estatus_id', 1)
                ->update(['estatus_id' => $diagnosticoIniciado]);
        }

        if ($diagnosticoTerminado) {
            DB::table('seguimiento_unidades')
                ->where('estatus_id', 2)
                ->update(['estatus_id' => $diagnosticoTerminado]);
        }

        Schema::table('seguimiento_unidades', function (Blueprint $table) {
            $table->foreign('estatus_id')->references('id')->on('estatus');
        });
    }

    public function down(): void
    {
        Schema::table('seguimiento_unidades', function (Blueprint $table) {
            $table->dropForeign(['estatus_id']);
        });

        Schema::table('seguimiento_unidades', function (Blueprint $table) {
            $table->renameColumn('estatus_id', 'tipo_id');
        });

        Schema::table('seguimiento_unidades', function (Blueprint $table) {
            $table->foreign('tipo_id')->references('id')->on('tipos');
        });
    }
};
