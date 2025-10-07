<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\NivelesCombustible as NivelesCombustibleModel;
class NivelesCombustible extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
       NivelesCombustibleModel::insert([
           ['descripcion' => 'Lleno'],
           ['descripcion' => '3/4'],
           ['descripcion' => '1/2'],
           ['descripcion' => '1/4'],
           ['descripcion' => 'Vacio'],
        ]);
    }
}
