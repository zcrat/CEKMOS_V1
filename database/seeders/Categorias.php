<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Categorias as CategoriasModel;
class Categorias extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {   
        $categorias = [
            ['descripcion' => 'Usuarios Taller'],
            ['descripcion' => 'Presupuestos'],
            ['descripcion' => 'Vehiculos'],
            ['descripcion' => 'Notificaciones'],
            ['descripcion' => 'Archivos Orden Servicio'],
            ['descripcion' => 'Rutas Tipos Archivo'],
            ['descripcion' => 'Facturas'],
        ];
        foreach ($categorias as $data) {
            CategoriasModel::create($data);
        }
    }
}
