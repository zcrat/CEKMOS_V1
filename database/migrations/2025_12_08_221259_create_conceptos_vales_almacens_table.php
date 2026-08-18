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
        Schema::create('conceptos_almacen', function (Blueprint $table) {
            $table->id();
            $table->string('clave')->nullable();
            $table->text('descripcion');
            $table->decimal('costo',8,2)->nullable();
            $table->decimal('venta',8,2)->nullable();
            $table->timestamps();
        });

        DB::statement(
            'CREATE UNIQUE INDEX [conceptos_almacen_clave_unique]
             ON [conceptos_almacen] ([clave])
             WHERE [clave] IS NOT NULL'
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('conceptos_almacen');
    }
};
