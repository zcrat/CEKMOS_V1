<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
       Schema::table('datos_entradas', function (Blueprint $table) {
            $table->renameColumn('kilomentraje', 'kilometraje');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
         Schema::table('datos_entradas', function (Blueprint $table) {
            $table->renameColumn('kilometraje', 'kilomentraje');
        });
    }
};
