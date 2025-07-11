<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
{
    Schema::table('regimes_fiscales', function (Blueprint $table) {
        $table->unique('clave'); // Agrega índice único
    });
}

public function down()
{
    Schema::table('regimes_fiscales', function (Blueprint $table) {
        $table->dropUnique(['clave']); // Elimina índice único
    });
}
};
