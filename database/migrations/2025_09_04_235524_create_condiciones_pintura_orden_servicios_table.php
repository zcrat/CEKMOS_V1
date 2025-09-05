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
        Schema::create('condiciones_pintura_orden_servicios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('orden_servicio_id')->constrained('ordenes_servicio');
            $table->boolean('decolorada')->default(false);
            $table->boolean('emblemas_completos')->default(false);
            $table->boolean('color_no_igual')->default(false);
            $table->boolean('logos')->default(false);
            $table->boolean('exeso_rayones')->default(false);
            $table->boolean('exeso_rociado')->default(false);
            $table->boolean('pequenias_grietas')->default(false);
            $table->boolean('danios_granizado')->default(false);
            $table->boolean('carroceria_golpes')->default(false);
            $table->boolean('lluvia_acido')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('condiciones_pintura_orden_servicios');
    }
};
