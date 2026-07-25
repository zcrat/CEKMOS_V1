<?php

namespace Database\Seeders;

use App\Models\CategoriasConceptosDisponibles;
use App\Models\Tipos;
use Illuminate\Database\Seeder;
use RuntimeException;

class CategoriasConceptosDisponiblesSeeder extends Seeder
{
    public function run(): void
    {
        $tiposPresupuesto = Tipos::query()
            ->where('categoria_id', 2)
            ->get(['id', 'descripcion']);
        $categoriasConcepto = Tipos::query()
            ->where('categoria_id', 7)
            ->get(['id', 'descripcion']);

        if ($tiposPresupuesto->isEmpty() || $categoriasConcepto->isEmpty()) {
            throw new RuntimeException(
                'Primero deben existir los tipos de presupuesto y las categorías de conceptos.'
            );
        }

        CategoriasConceptosDisponibles::query()
            ->whereIn('tipo_presupuesto_id', $tiposPresupuesto->pluck('id'))
            ->delete();

        foreach ($tiposPresupuesto as $tipoPresupuesto) {
            $descripcionTipo = mb_strtolower($tipoPresupuesto->descripcion, 'UTF-8');

            $categoriasPermitidas = $categoriasConcepto->filter(
                function (Tipos $categoria) use ($descripcionTipo): bool {
                    $esPreventiva = str_contains(
                        mb_strtolower($categoria->descripcion, 'UTF-8'),
                        'servicio preventivo'
                    );

                    if (str_contains($descripcionTipo, 'prevent')) {
                        return $esPreventiva;
                    }

                    if (str_contains($descripcionTipo, 'correct')) {
                        return ! $esPreventiva;
                    }

                    return true;
                }
            );

            foreach ($categoriasPermitidas as $categoria) {
                CategoriasConceptosDisponibles::create([
                    'tipo_presupuesto_id' => $tipoPresupuesto->id,
                    'categoria_concepto_id' => $categoria->id,
                ]);
            }
        }
    }
}
