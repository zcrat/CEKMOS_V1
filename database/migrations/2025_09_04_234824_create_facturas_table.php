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
        Schema::create('facturas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empresa_id')->constrained('empresas');
            $table->foreignId('emisor_id')->constrained('emisores');
            $table->foreignId('user_id')->constrained('users');
            $table->text('xml');
            $table->text('pdf');
            $table->text('acuse')->nullable();
            $table->foreignId('estatus_id')->constrained('estatus');
            $table->decimal('monto',10,2);
            $table->string('folio');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('facturas');
    }
};
