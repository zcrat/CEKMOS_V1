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
        Schema::create('presupuestos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('orden_servicio_id')->constrained('ordenes_servicio');
            $table->text('observaciones');
            $table->text('descripcion_mo');
            $table->text('garantia');
            $table->string('folio');
            $table->dateTime('vigencia');
            $table->foreignId('factura_id')->constrained('facturas')->nullable();
            $table->foreignId('tipo_id')->constrained('tipos');
            $table->foreignId('estatus_id')->constrained('estatus');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('presupuestos');
    }
};
