<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('conceptos_per_vale_almacen', function (Blueprint $table) {
            $table->dateTime('fecha_entrega')->nullable()->index();
        });
    }

    public function down(): void
    {
        Schema::table('conceptos_per_vale_almacen', function (Blueprint $table) {
            $table->dropIndex(['fecha_entrega']);
            $table->dropColumn('fecha_entrega');
        });
    }
};
