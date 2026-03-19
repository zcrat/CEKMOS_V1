<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\VehiculosConceptos as ModelVehiculos;
class VehiculosConceptos extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    
    {
        $data=[
            ['descripcion' => '4 Cilindros'],
            ['descripcion' => '6 Cilindros'],
            ['descripcion' => '8 Cilindros'],
        ];
        foreach ($data as $registro) {
            ModelVehiculos::create($registro);
        }
    }
}
