<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('categorias_conceptos_disponibles', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tipo_presupuesto_id');
            $table->unsignedBigInteger('categoria_concepto_id');
            $table->timestamps();

            $table->foreign(
                'tipo_presupuesto_id',
                'categorias_disponibles_tipo_presupuesto_fk'
            )->references('id')->on('tipos');
            $table->foreign(
                'categoria_concepto_id',
                'categorias_disponibles_categoria_concepto_fk'
            )->references('id')->on('tipos');
            $table->unique(
                ['tipo_presupuesto_id', 'categoria_concepto_id'],
                'categorias_conceptos_disponibles_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('categorias_conceptos_disponibles');
    }
};
