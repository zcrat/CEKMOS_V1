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
            ['descripcion' => 'Archivos'],
            ['descripcion' => 'Acciones'],
            ['descripcion' => 'Facturas'],
            ['descripcion' => 'Conceptos Presupuesto'],
            ['descripcion' => 'Notificaciones'],
            ['descripcion' => 'Hoja de Conceptos'],
            ['descripcion' => 'Inspeccion Vehicular'],
            ['descripcion' => 'Recepcion Vehicular'],
            ['descripcion' => 'Seguimiento Ordenes Servicio'],
            ['descripcion' => 'Vales De Almacen'],
        ];
        foreach ($categorias as $data) {
            CategoriasModel::create($data);
        }
    }
}
