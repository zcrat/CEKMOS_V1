<?php

namespace Database\Seeders;

use App\Models\Motores as Motor;
use Illuminate\Database\Seeder;

class Motores extends Seeder
{
    public function run(): void
    {
        $motores = [
            '4 cilindros gasolina',
            '4 cilindros diésel',
            '6 cilindros gasolina',
            '6 cilindros diésel',
            '8 cilindros gasolina',
            '8 cilindros diésel',
            '10 cilindros gasolina',
            '10 cilindros diésel',
            'eléctrico',
        ];

        foreach ($motores as $descripcion) {
            Motor::firstOrCreate(compact('descripcion'));
        }
    }
}
