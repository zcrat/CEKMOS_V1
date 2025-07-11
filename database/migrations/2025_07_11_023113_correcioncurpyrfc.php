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
       Schema::table('employes', function (Blueprint $table) {
            $table->unique('curp'); // Agrega índice único
            $table->unique('rfc'); // Agrega índice único
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employes', function (Blueprint $table) {
            $table->dropUnique(['curp']); // Elimina índice único
            $table->dropUnique(['rfc']); // Elimina índice único
        });
    }
};
