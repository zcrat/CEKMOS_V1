<?php

namespace Database\Seeders;

use App\Models\CategoriasSat;
use App\Models\ConceptosPresupuestos;
use App\Models\CostosConceptosPresupuestos;
use App\Models\OrdenesServicio;
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
        $ordenesDemo = OrdenesServicio::query()
            ->whereHas(
                'presupuestos',
                fn ($query) => $query->where('folio', 'like', 'DEMO-PRE-%')
            )
            ->with(['vehiculo_concepto', 'modulo_ordenes_servicio'])
            ->whereHas('vehiculo_concepto')
            ->whereHas('modulo_ordenes_servicio')
            ->orderBy('id')
            ->get()
            ->unique('modulo_orden_id')
            ->values();
        $usuario = User::query()->orderBy('id')->first();
        $tipos = Tipos::query()
            ->where('categoria_id', 7)
            ->orderBy('id')
            ->get();

        if ($ordenesDemo->isEmpty()) {
            throw new RuntimeException('Se requiere al menos una orden demo con presupuesto.');
        }

        if ($tipos->isEmpty()) {
            throw new RuntimeException('Se requiere al menos un tipo de concepto.');
        }

        if (! $usuario) {
            throw new RuntimeException('Se requiere al menos un usuario.');
        }

        $conceptos = [
            ['descripcion' => 'Cambio de aceite y filtro de motor', 'categoria' => 'Servicio Preventivo Menor', 'refaccion' => 950.00, 'mano_obra' => 450.00],
            ['descripcion' => 'Afinación mayor de motor', 'categoria' => 'Servicio Preventivo Mayor', 'refaccion' => 2450.00, 'mano_obra' => 1200.00],
            ['descripcion' => 'Reemplazo de balatas delanteras', 'categoria' => 'Sistema de Frenos', 'refaccion' => 1850.00, 'mano_obra' => 750.00],
            ['descripcion' => 'Reemplazo de balatas traseras', 'categoria' => 'Sistema de Frenos', 'refaccion' => 1750.00, 'mano_obra' => 750.00],
            ['descripcion' => 'Rectificación de discos delanteros', 'categoria' => 'Sistema de Frenos', 'refaccion' => 600.00, 'mano_obra' => 900.00],
            ['descripcion' => 'Cambio de amortiguadores delanteros', 'categoria' => 'Sistema de suspension y dirección', 'refaccion' => 4200.00, 'mano_obra' => 1600.00],
            ['descripcion' => 'Cambio de amortiguadores traseros', 'categoria' => 'Sistema de suspension y dirección', 'refaccion' => 3600.00, 'mano_obra' => 1400.00],
            ['descripcion' => 'Alineación y balanceo', 'categoria' => 'Sistema de suspension y dirección', 'refaccion' => 0.00, 'mano_obra' => 850.00],
            ['descripcion' => 'Servicio al sistema de enfriamiento', 'categoria' => 'Sistema de Enfriamiento', 'refaccion' => 1250.00, 'mano_obra' => 800.00],
            ['descripcion' => 'Cambio de batería', 'categoria' => 'Sistema Electrico', 'refaccion' => 2850.00, 'mano_obra' => 350.00],
            ['descripcion' => 'Diagnóstico del sistema eléctrico', 'categoria' => 'Sistema Electrico', 'refaccion' => 0.00, 'mano_obra' => 950.00],
            ['descripcion' => 'Cambio de banda de accesorios', 'categoria' => 'Sistema de motor', 'refaccion' => 1100.00, 'mano_obra' => 650.00],
            ['descripcion' => 'Lavado de inyectores', 'categoria' => 'Sistema de motor', 'refaccion' => 450.00, 'mano_obra' => 1050.00],
            ['descripcion' => 'Servicio de aire acondicionado', 'categoria' => 'Sistema de aire acondicionado', 'refaccion' => 900.00, 'mano_obra' => 1100.00],
            ['descripcion' => 'Cambio de aceite de transmisión', 'categoria' => 'Sistema de Transmision', 'refaccion' => 2200.00, 'mano_obra' => 950.00],
            ['descripcion' => 'Reparación de fuga de aceite', 'categoria' => 'Sistema de motor', 'refaccion' => 1350.00, 'mano_obra' => 1500.00],
            ['descripcion' => 'Cambio de bujías', 'categoria' => 'Sistema de motor', 'refaccion' => 1250.00, 'mano_obra' => 550.00],
            ['descripcion' => 'Revisión general de seguridad', 'categoria' => 'Adaptaciones y servicios', 'refaccion' => 0.00, 'mano_obra' => 650.00],
            ['descripcion' => 'Cambio de filtro de aire', 'categoria' => 'Servicio Preventivo Menor', 'refaccion' => 480.00, 'mano_obra' => 220.00],
            ['descripcion' => 'Cambio de filtro de combustible', 'categoria' => 'Servicio Preventivo Menor', 'refaccion' => 780.00, 'mano_obra' => 420.00],
        ];

        DB::transaction(function () use ($conceptos, $ordenesDemo, $tipos, $usuario) {
            $categoriaSat = CategoriasSat::updateOrCreate(
                ['codigo_sat' => '78181500'],
                ['descripcion' => 'Servicios de mantenimiento y reparación de vehículos']
            );

            $unidadSat = UnidadesSat::updateOrCreate(
                ['codigo' => 'E48'],
                ['descripcion' => 'Unidad de servicio']
            );

            $tiposPorDescripcion = $tipos->keyBy(
                fn (Tipos $tipo) => mb_strtolower($tipo->descripcion, 'UTF-8')
            );
            $numerosEsperados = $ordenesDemo->flatMap(
                fn (OrdenesServicio $orden) => collect($conceptos)->keys()->map(
                    fn (int $index) => 'DEMO-CON-M'.$orden->modulo_orden_id.'-'
                        .str_pad((string) ($index + 1), 3, '0', STR_PAD_LEFT)
                )
            )->values();

            ConceptosPresupuestos::query()
                ->where('num', 'like', 'DEMO-CON-%')
                ->whereNotIn('num', $numerosEsperados)
                ->whereDoesntHave('presupuestos_asignados')
                ->get()
                ->each->delete();

            foreach ($ordenesDemo as $orden) {
                $availability = VehiculosConceptosDisponibles::withTrashed()
                    ->firstOrNew([
                        'vehiculo_concepto_id' => $orden->vehiculo_concepto_id,
                        'modulo_orden_id' => $orden->modulo_orden_id,
                    ]);
                $availability->deleted_at = null;
                $availability->save();

                foreach ($conceptos as $index => $datos) {
                    $numero = 'DEMO-CON-M'.$orden->modulo_orden_id.'-'
                        .str_pad((string) ($index + 1), 3, '0', STR_PAD_LEFT);
                    $tipo = $tiposPorDescripcion->get(
                        mb_strtolower($datos['categoria'], 'UTF-8')
                    );

                    if (! $tipo) {
                        throw new RuntimeException(
                            "No existe la categoría demo {$datos['categoria']}."
                        );
                    }

                    $concepto = ConceptosPresupuestos::updateOrCreate(
                        ['num' => $numero],
                        [
                            'descripcion' => $datos['descripcion'],
                            'garantia_dias' => 30 + (($index % 3) * 30),
                            'fijo' => false,
                            'tipo_id' => $tipo->id,
                            'modulo_orden_servicio_id' => $orden->modulo_orden_id,
                            'categoria_sat_id' => $categoriaSat->id,
                            'unidad_sat_id' => $unidadSat->id,
                        ]
                    );

                    CostosConceptosPresupuestos::query()
                        ->where('concepto_presupuesto_id', $concepto->id)
                        ->where('vehiculo_concepto_id', '!=', $orden->vehiculo_concepto_id)
                        ->delete();

                    CostosConceptosPresupuestos::updateOrCreate(
                        [
                            'concepto_presupuesto_id' => $concepto->id,
                            'vehiculo_concepto_id' => $orden->vehiculo_concepto_id,
                        ],
                        [
                            'usuario_id' => $usuario->id,
                            'p_refaccion' => $datos['refaccion'],
                            'p_mano_obra' => $datos['mano_obra'],
                            'p_total' => $datos['refaccion'] + $datos['mano_obra'],
                        ]
                    );
                }
            }
        });
    }
}
