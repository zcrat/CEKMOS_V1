<?php

namespace Database\Seeders;

use App\Models\CategoriasSat;
use App\Models\ConceptosPresupuestos;
use App\Models\CostosConceptosPresupuestos;
use App\Models\Tipos;
use App\Models\UnidadesSat;
use App\Models\User;
use App\Models\VehiculosConceptosDisponibles;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class ConceptosPresupuestosDemoSeeder extends Seeder
{
    public function run(): void
    {
        $disponibilidades = VehiculosConceptosDisponibles::query()
            ->with(['vehiculo_concepto', 'modulo_ordenes_servicio'])
            ->whereHas('vehiculo_concepto')
            ->whereHas('modulo_ordenes_servicio')
            ->orderBy('id')
            ->get();
        $usuario = User::query()->orderBy('id')->first();
        $tipos = Tipos::query()
            ->where('categoria_id', 7)
            ->orderBy('id')
            ->get();

        if ($tipos->isEmpty()) {
            $tipos = Tipos::query()->orderBy('id')->get();
        }

        if ($disponibilidades->isEmpty()) {
            throw new RuntimeException('Se requiere al menos un vehículo disponible para un módulo.');
        }

        if ($tipos->isEmpty()) {
            throw new RuntimeException('Se requiere al menos un tipo de concepto.');
        }

        if (! $usuario) {
            throw new RuntimeException('Se requiere al menos un usuario.');
        }

        $conceptos = [
            ['descripcion' => 'Cambio de aceite y filtro de motor', 'refaccion' => 950.00, 'mano_obra' => 450.00],
            ['descripcion' => 'Afinación mayor de motor', 'refaccion' => 2450.00, 'mano_obra' => 1200.00],
            ['descripcion' => 'Reemplazo de balatas delanteras', 'refaccion' => 1850.00, 'mano_obra' => 750.00],
            ['descripcion' => 'Reemplazo de balatas traseras', 'refaccion' => 1750.00, 'mano_obra' => 750.00],
            ['descripcion' => 'Rectificación de discos delanteros', 'refaccion' => 600.00, 'mano_obra' => 900.00],
            ['descripcion' => 'Cambio de amortiguadores delanteros', 'refaccion' => 4200.00, 'mano_obra' => 1600.00],
            ['descripcion' => 'Cambio de amortiguadores traseros', 'refaccion' => 3600.00, 'mano_obra' => 1400.00],
            ['descripcion' => 'Alineación y balanceo', 'refaccion' => 0.00, 'mano_obra' => 850.00],
            ['descripcion' => 'Servicio al sistema de enfriamiento', 'refaccion' => 1250.00, 'mano_obra' => 800.00],
            ['descripcion' => 'Cambio de batería', 'refaccion' => 2850.00, 'mano_obra' => 350.00],
            ['descripcion' => 'Diagnóstico del sistema eléctrico', 'refaccion' => 0.00, 'mano_obra' => 950.00],
            ['descripcion' => 'Cambio de banda de accesorios', 'refaccion' => 1100.00, 'mano_obra' => 650.00],
            ['descripcion' => 'Lavado de inyectores', 'refaccion' => 450.00, 'mano_obra' => 1050.00],
            ['descripcion' => 'Servicio de aire acondicionado', 'refaccion' => 900.00, 'mano_obra' => 1100.00],
            ['descripcion' => 'Cambio de aceite de transmisión', 'refaccion' => 2200.00, 'mano_obra' => 950.00],
            ['descripcion' => 'Reparación de fuga de aceite', 'refaccion' => 1350.00, 'mano_obra' => 1500.00],
            ['descripcion' => 'Cambio de bujías', 'refaccion' => 1250.00, 'mano_obra' => 550.00],
            ['descripcion' => 'Revisión general de seguridad', 'refaccion' => 0.00, 'mano_obra' => 650.00],
            ['descripcion' => 'Cambio de filtro de aire', 'refaccion' => 480.00, 'mano_obra' => 220.00],
            ['descripcion' => 'Cambio de filtro de combustible', 'refaccion' => 780.00, 'mano_obra' => 420.00],
        ];

        DB::transaction(function () use ($conceptos, $disponibilidades, $tipos, $usuario) {
            $categoriaSat = CategoriasSat::updateOrCreate(
                ['codigo_sat' => '78181500'],
                ['descripcion' => 'Servicios de mantenimiento y reparación de vehículos']
            );

            $unidadSat = UnidadesSat::updateOrCreate(
                ['codigo' => 'E48'],
                ['descripcion' => 'Unidad de servicio']
            );

            foreach ($conceptos as $index => $datos) {
                $numero = 'DEMO-CON-'.str_pad((string) ($index + 1), 3, '0', STR_PAD_LEFT);
                $disponibilidad = $disponibilidades[$index % $disponibilidades->count()];
                $modulo = $disponibilidad->modulo_ordenes_servicio;
                $vehiculo = $disponibilidad->vehiculo_concepto;
                $tipo = $tipos[$index % $tipos->count()];

                $concepto = ConceptosPresupuestos::updateOrCreate(
                    ['num' => $numero],
                    [
                        'descripcion' => $datos['descripcion'],
                        'garantia_dias' => 30 + (($index % 3) * 30),
                        'fijo' => false,
                        'tipo_id' => $tipo->id,
                        'modulo_orden_servicio_id' => $modulo->id,
                        'categoria_sat_id' => $categoriaSat->id,
                        'unidad_sat_id' => $unidadSat->id,
                    ]
                );

                CostosConceptosPresupuestos::query()
                    ->where('concepto_presupuesto_id', $concepto->id)
                    ->where('vehiculo_concepto_id', '!=', $vehiculo->id)
                    ->delete();

                CostosConceptosPresupuestos::updateOrCreate(
                    [
                        'concepto_presupuesto_id' => $concepto->id,
                        'vehiculo_concepto_id' => $vehiculo->id,
                    ],
                    [
                        'usuario_id' => $usuario->id,
                        'p_refaccion' => $datos['refaccion'],
                        'p_mano_obra' => $datos['mano_obra'],
                        'p_total' => $datos['refaccion'] + $datos['mano_obra'],
                    ]
                );
            }
        });
    }
}
