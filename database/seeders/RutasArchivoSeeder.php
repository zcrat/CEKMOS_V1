<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\RutasArchivo;
class RutasArchivoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {   
        $paths = [
            ['disk' => 'public','folder' =>'archivos/ordenes_servicio/firmas','tipo_id'=>25,'estatus_id'=>21],
            ['disk' => 'public','folder' =>'archivos/ordenes_servicio/carros','tipo_id'=>26,'estatus_id'=>21],
            ['disk' => 'public','folder' =>'archivos/ordenes_servicio/evidencias','tipo_id'=>63,'estatus_id'=>21],
        ];
        foreach ($paths as $data) {
            RutasArchivo::create($data);
        }
    }
}
