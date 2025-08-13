<?php

namespace Database\Seeders;

use App\Models\Modulos as ModulosModel;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class modulos extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $modulos=['CFE','CFB','ECO'];
       foreach ($modulos as $modulo) {
            ModulosModel::create([
                'nombre' => $modulo,]);
        } 
    }
}
