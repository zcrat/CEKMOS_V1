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
            ['descripcion' => 'Camioneta', 'categoria_id' => 3],
            ['descripcion' => 'Coche', 'categoria_id' => 3],
            ['descripcion' => 'Vagoneta', 'categoria_id' => 3],
            ['descripcion' => 'Camion', 'categoria_id' => 3],
            ['descripcion' => 'Maquinaria', 'categoria_id' => 3],
            ['descripcion' => 'Motos', 'categoria_id' => 3],
            ['descripcion' => 'Fotos Nuevas', 'categoria_id' => 4],
            ['descripcion' => 'Fotos Instaladas', 'categoria_id' => 4],
            ['descripcion' => 'Fotos Viejas', 'categoria_id' => 4],
            ['descripcion' => 'Reporte Anomalías', 'categoria_id' => 4],
            ['descripcion' => 'Entrada', 'categoria_id' => 4],
            ['descripcion' => 'Orden Servicio', 'categoria_id' => 4],
            ['descripcion' => 'Factura PDF Externa', 'categoria_id' => 4],
            ['descripcion' => 'Factura XML Externa', 'categoria_id' => 4],
            ['descripcion' => 'Acuse', 'categoria_id' => 4],
            ['descripcion' => 'Caja', 'categoria_id' => 4],
            ['descripcion' => 'Retrasos RV', 'categoria_id' => 4],
            ['descripcion' => 'Salidas RV', 'categoria_id' => 4],
            ['descripcion' => 'Pago', 'categoria_id' => 4],
            ['descripcion' => 'Carro Detalles', 'categoria_id' => 4],
            ['descripcion' => 'Firma RV', 'categoria_id' => 4],
            ['descripcion' => 'Firma Iv', 'categoria_id' => 4],
            ['descripcion' => 'Firma IV Cliente', 'categoria_id' => 4],
            ['descripcion' => 'Pago', 'categoria_id' => 4],
            ['descripcion' => 'Crear', 'categoria_id' => 5],
            ['descripcion' => 'Eliminar', 'categoria_id' => 5],
            ['descripcion' => 'Actualizar', 'categoria_id' => 5],
            ['descripcion' => 'Presupuesto', 'categoria_id' => 6],
            ['descripcion' => 'Nomina', 'categoria_id' => 6],
            ['descripcion' => 'Sistema de Frenos', 'categoria_id' => 7],
            ['descripcion' => 'Sistema de Enfriamiento', 'categoria_id' => 7],
            ['descripcion' => 'Sistema Electrico', 'categoria_id' => 7],
            ['descripcion' => 'Sistema de Transmision', 'categoria_id' => 7],
            ['descripcion' => 'Sistema de suspension y dirección', 'categoria_id' => 7],
            ['descripcion' => 'Sistema de motor', 'categoria_id' => 7],
            ['descripcion' => 'Sistema de escape', 'categoria_id' => 7],
            ['descripcion' => 'Sistema de aire acondicionado', 'categoria_id' => 7],
            ['descripcion' => 'Adaptaciones y servicios', 'categoria_id' => 7],
            ['descripcion' => 'Servicio Preventivo Mayor', 'categoria_id' => 7],
            ['descripcion' => 'Servicio Preventivo Menor', 'categoria_id' => 7],
            ['descripcion' => 'Sistema Hidraulico', 'categoria_id' => 7],
            ['descripcion' => 'LLantas', 'categoria_id' => 7],
            ['descripcion' => 'Recordatorios', 'categoria_id' => 8],
            ['descripcion' => 'Informativa', 'categoria_id' => 8],
            ['descripcion' => 'Alerta O Advertencia', 'categoria_id' => 8],
            ['descripcion' => 'Critica', 'categoria_id' => 8],
            ['descripcion' => 'Personalizada', 'categoria_id' => 8],
            ['descripcion' => 'Zonas', 'categoria_id' => 7],
        ];
        foreach ($usuarios_taller as $data) {
            TiposModel::create($data);
        }
    }
}
