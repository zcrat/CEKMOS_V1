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
        Schema::table('cfdis_avaliables', function (Blueprint $table) {
            $table->dropColumn('cfdi_id');
        });
        Schema::table('cfdis_avaliables', function (Blueprint $table) {
            $table->foreignId('uso_cfdi_id')->references('id')->on('cfdis');
        });
        Schema::table('cfdis_avaliables', function (Blueprint $table) {
            $table->dropColumn('regimen_fiscal');
        });
        Schema::table('cfdis_avaliables', function (Blueprint $table) {
            $table->foreignId('regimen_fiscal_id')->references('id')->on('regimes_fiscales');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cfdis_avaliables', function (Blueprint $table) {
            $table->dropColumn('regimen_fiscal_id');
        });
        Schema::table('cfdis_avaliables', function (Blueprint $table) {
            $table->dropColumn('uso_cfdi_id');
        });
    }
};
