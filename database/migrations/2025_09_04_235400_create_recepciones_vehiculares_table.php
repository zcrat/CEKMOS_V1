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
        Schema::create('recepciones_vehiculares', function (Blueprint $table) {
            $table->id();
            $table->foreignId('orden_servicio_id')->unique()->constrained('ordenes_servicio');
            $table->boolean('is_ficticia')->default(false);
            $table->boolean('cambiar_archivos')->default(false);
            $table->text('indicaciones_cliente')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recepciones_vehiculares');
    }
};
