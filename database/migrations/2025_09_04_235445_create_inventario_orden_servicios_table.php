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
        Schema::create('inventario_orden_servicios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('recepcion_vehicular_id')->constrained('recepciones_vehiculares');
            $table->boolean('llanta')->default(false);
            $table->boolean('cubreruedas')->default(false);
            $table->boolean('cables_corriente')->default(false);
            $table->boolean('candado_ruedas')->default(false);
            $table->boolean('estuche_herramientas')->default(false);
            $table->boolean('gato')->default(false);
            $table->boolean('llave_tuercas')->default(false);
            $table->boolean('trajeta_circulacion')->default(false);
            $table->boolean('triangulo_seguridad')->default(false);
            $table->boolean('extinguidor')->default(false);
            $table->boolean('placas')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventario_orden_servicios');
    }
};
