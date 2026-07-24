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
        Schema::create('costos_conceptos_presupuestos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('concepto_presupuesto_id');
            $table->foreignId('vehiculo_concepto_id');
            $table->foreignId('usuario_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();
            $table->decimal('p_refaccion', 10, 2);
            $table->decimal('p_mano_obra', 10, 2);
            $table->decimal('p_total', 10, 2);
            $table->timestamps();

            $table->foreign('concepto_presupuesto_id', 'ccp_concepto_fk')
                ->references('id')
                ->on('conceptos_presupuestos')
                ->cascadeOnDelete();
            $table->foreign('vehiculo_concepto_id', 'ccp_vehiculo_fk')
                ->references('id')
                ->on('vehiculos_conceptos');

            $table->unique(
                ['concepto_presupuesto_id', 'vehiculo_concepto_id'],
                'ccp_concepto_vehiculo_unique'
            );
            $table->index('vehiculo_concepto_id', 'ccp_vehiculo_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('costos_conceptos_presupuestos');
    }
};
