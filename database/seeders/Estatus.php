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
            ['descripcion' => 'Pendiente De Autorizar', 'categoria_id' => 2],
            ['descripcion' => 'Autorizacion Aprobada', 'categoria_id' => 2],
            ['descripcion' => 'Pendiente De Pago', 'categoria_id' => 2],
            ['descripcion' => 'Pendiente De Terminar', 'categoria_id' => 2],
            ['descripcion' => 'Terminado Con Factura', 'categoria_id' => 2],
            ['descripcion' => 'Autorizacion Denegada', 'categoria_id' => 2],
            ['descripcion' => 'Pago Denegado', 'categoria_id' => 2],
            ['descripcion' => 'Solo Terminado', 'categoria_id' => 2],
            ['descripcion' => 'Petición de cancelación realizada exitosamente', 'categoria_id' => 6],
            ['descripcion' => 'Cancelacion exitosa', 'categoria_id' => 6],
            ['descripcion' => 'Solicitud realizada anteriormente, espere 72 horas...', 'categoria_id' => 6],
            ['descripcion' => 'Límite de solicitudes de cancelación alcanzado.', 'categoria_id' => 6],
            ['descripcion' => 'SIN DAÑO VISIBLE', 'categoria_id' => 11],
            ['descripcion' => 'OPERACIONAL', 'categoria_id' => 11],
            ['descripcion' => 'FALTA OBJETO', 'categoria_id' => 11],
            ['descripcion' => 'DANADA', 'categoria_id' => 11],
            ['descripcion' => 'REPARACION NECESARIA', 'categoria_id' => 11],
            ['descripcion' => 'NO APLICA', 'categoria_id' => 11],
            ['descripcion' => 'Procesando', 'categoria_id' => 4],
            ['descripcion' => 'Exitoso', 'categoria_id' => 4],
            ['descripcion' => 'Erroneo', 'categoria_id' => 4],
        ];
        foreach ($estatus as $data) {
            EstatusModel::create($data);
        }
    }
}
