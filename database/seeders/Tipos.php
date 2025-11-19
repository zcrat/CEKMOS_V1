<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Tipos as TiposModel;
class Tipos extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $usuarios_taller = [
            ['descripcion' => 'Administrador de Transporte', 'categoria_id' => 1],
            ['descripcion' => 'Jefe de Proceso', 'categoria_id' => 1],
            ['descripcion' => 'Trabajador', 'categoria_id' => 1],
            ['descripcion' => 'Técnico', 'categoria_id' => 1],
            ['descripcion' => 'Correctivo', 'categoria_id' => 2],
            ['descripcion' => 'Preventino', 'categoria_id' => 2],
            ['descripcion' => 'Ambos', 'categoria_id' => 2],
        ];
        foreach ($usuarios_taller as $data) {
            TiposModel::create($data);
        }
    }
}
