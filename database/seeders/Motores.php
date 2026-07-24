<?php

namespace Database\Seeders;

use App\Models\Motores as Motor;
use Illuminate\Database\Seeder;

class Motores extends Seeder
{
    public function run(): void
    {
        $motores = [
            '4 cilindros',
            '6 cilindros',
            '8 cilindros',
            '10 cilindros',
            'Eléctrico',
        ];

        foreach ($motores as $descripcion) {
            Motor::firstOrCreate(compact('descripcion'));
        }
    }
}
