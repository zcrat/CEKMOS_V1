<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Estatus as EstatusModel;

class Estatus extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $estatus = [
            ['descripcion' => 'Pendiente De Enviar', 'categoria_id' => 2],
            ['descripcion' => 'Pendiente De Aprobar', 'categoria_id' => 2],
            ['descripcion' => 'Pendiente De Terminar', 'categoria_id' => 2],
            ['descripcion' => 'Pendiente De Pago', 'categoria_id' => 2],
            ['descripcion' => 'Pendiente De Factura', 'categoria_id' => 2],
            ['descripcion' => 'Aprobacion Denegada', 'categoria_id' => 2],
            ['descripcion' => 'Pago Denegado', 'categoria_id' => 2],
            ['descripcion' => 'Registrada', 'categoria_id' => 6],
            ['descripcion' => 'Petición de cancelación realizada exitosamente', 'categoria_id' => 6],
            ['descripcion' => 'Cancelacion exitosa', 'categoria_id' => 6],
            ['descripcion' => 'Solicitud realizada anteriormente, espere 72 horas...', 'categoria_id' => 6],
            ['descripcion' => 'Límite de solicitudes de cancelación alcanzado.', 'categoria_id' => 6],
        ];
        foreach ($estatus as $data) {
            EstatusModel::create($data);
        }
    }
}
