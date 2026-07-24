<?php

namespace Database\Seeders;

use App\Models\Marcas;
use App\Models\Modelos;
use App\Models\Motores;
use App\Models\VehiculosConceptos as ModelVehiculos;
use Illuminate\Database\Seeder;

class VehiculosConceptos extends Seeder
{
    public function run(): void
    {
        $marca = Marcas::firstOrCreate([
            'descripcion' => 'Sin Especificar',
        ]);

        $motores = Motores::query()
            ->orderBy('id')
            ->limit(5)
            ->get();

        foreach ($motores as $motor) {
            $modelo = Modelos::firstOrCreate([
                'descripcion' => 'Sin Especificar',
                'marca_id' => $marca->id,
                'motor_id' => $motor->id,
            ]);

            ModelVehiculos::firstOrCreate(
                ['modelo_id' => $modelo->id],
                [
                    'descripcion' => "Todos Los Modelos de {$motor->descripcion}",
                    'años' => [],
                ]
            );
        }
    }
}
