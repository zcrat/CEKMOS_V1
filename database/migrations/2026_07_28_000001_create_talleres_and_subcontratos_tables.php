<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('talleres', function (Blueprint $table) {
            $table->id();
            $table->string('descripcion')->unique();
            $table->boolean('externo')->default(false);
        });

        $tallerPrincipalId = 1;
        DB::table('talleres')->insert([
            'descripcion' => 'Taller principal',
            'externo' => false,
        ]);

        Schema::table('users', function (Blueprint $table) use ($tallerPrincipalId) {
            $table->foreignId('taller_id')
                ->default($tallerPrincipalId)
                ->constrained('talleres');
        });

        Schema::table('ordenes_servicio', function (Blueprint $table) use ($tallerPrincipalId) {
            $table->foreignId('taller_id')
                ->default($tallerPrincipalId)
                ->constrained('talleres');
        });

        Schema::create('subcontratos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('orden_servicio_id')
                ->constrained('ordenes_servicio');
            $table->dateTime('fecha_inicio');
            $table->dateTime('fecha_fin')->nullable();
            $table->foreignId('user_id')->constrained('users');
        });

        Schema::table('subcontratos', function (Blueprint $table) {
            $table->index(
                ['orden_servicio_id', 'fecha_fin'],
                'subcontratos_orden_fecha_fin_index'
            );
        });

        if (DB::connection()->getDriverName() === 'sqlsrv') {
            DB::statement(
                'CREATE UNIQUE INDEX subcontratos_orden_activo_unique
                ON subcontratos (orden_servicio_id)
                WHERE fecha_fin IS NULL'
            );
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('subcontratos');

        Schema::table('ordenes_servicio', function (Blueprint $table) {
            $table->dropConstrainedForeignId('taller_id');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('taller_id');
        });

        Schema::dropIfExists('talleres');
    }
};
