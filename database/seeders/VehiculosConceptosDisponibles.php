<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\VehiculosConceptosDisponibles as ModelVehiculoDis;

class VehiculosConceptosDisponibles extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    
    {
        $data=[
            [ 
                'vehiculo_concepto_id'=>1,
                'modulo_orden_id'=>17,
            ],
            [ 
                'vehiculo_concepto_id'=>2,
                'modulo_orden_id'=>17,
            ],
            [ 
                'vehiculo_concepto_id'=>3,
                'modulo_orden_id'=>17,
            ],
        ];
        foreach ($data as $registro) {
            ModelVehiculoDis::create($registro);
        }
    }
}
