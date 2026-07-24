<?php

namespace Database\Seeders;

use App\Models\Archivos;
use App\Models\Clientes;
use App\Models\CondicionesPinturaRV;
use App\Models\DatosEntrada;
use App\Models\DatosSalida;
use App\Models\Empresas;
use App\Models\ExterioresRV;
use App\Models\InterioresRV;
use App\Models\InventarioRV;
use App\Models\ModuloOrdenesServicio;
use App\Models\NivelesCombustible;
use App\Models\OrdenesServicio;
use App\Models\RecepcionesVehiculares;
use App\Models\ResponsablesOrdenServicio;
use App\Models\RutasArchivo;
use App\Models\Ubicaciones;
use App\Models\User;
use App\Models\Vehiculos;
use App\Models\VehiculosConceptos;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class OrdenesDemoSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {
            $users = User::whereIn('usuario', ['zcrat', 'admin', 'foster'])->get()->values();
            if ($users->isEmpty()) {
                $users = User::take(3)->get()->values();
            }

            $empresa = Empresas::first();
            $cliente = Clientes::first();
            $vehiculo = Vehiculos::first();
            $concepto = VehiculosConceptos::first();
            $modulos = ModuloOrdenesServicio::inRandomOrder()->take(10)->get()->values();
            if ($modulos->isEmpty()) {
                $modulos = ModuloOrdenesServicio::all()->values();
            }

            $ubicacion = Ubicaciones::firstOrCreate([
                'nombre' => 'TALLER',
            ], [
                'descripcion' => 'TALLER',
            ]);

            $nivelComb = NivelesCombustible::inRandomOrder()->value('id') ?? 1;

            for ($i = 1; $i <= 10; $i++) {
                $user = $users[$i % max(1, $users->count())];
                $mod = $modulos[$i % max(1, $modulos->count())];

                $orden = sprintf('OS-%s-%04d', date('Y'), $i);
                $seguimiento = sprintf('SEQ-%s-%04d', date('Y'), $i);

                $os = OrdenesServicio::create([
                    'orden_servicio' => $orden,
                    'orden_seguimiento' => $seguimiento,
                    'orden_opcional' => null,
                    'modulo_orden_id' => optional($mod)->id ?? 1,
                    'vehiculo_id' => optional($vehiculo)->id ?? 1,
                    'vehiculo_concepto_id' => optional($concepto)->id ?? 1,
                    'user_id' => $user->id,
                    'empresa_id' => optional($empresa)->id ?? 1,
                    'cliente_id' => optional($cliente)->id ?? 1,
                    'diagnostico' => Carbon::now()->addHours(rand(1, 8)),
                    'fallas_reportadas' => 'Sin novedades relevantes',
                    'notas_retraso' => null,
                    'telefono' => '443-000-000'.($i % 10),
                    'ubicacion_id' => $ubicacion->id,
                ]);

                // Entrada
                DatosEntrada::create([
                    'fecha' => Carbon::now(),
                    'estimacion' => Carbon::now()->addDays(2),
                    'kilometraje' => 10000 + ($i * 123),
                    'gasolina' => $nivelComb,
                    'orden_servicio_id' => $os->id,
                ]);

                // Salida
                DatosSalida::create([
                    'fecha' => Carbon::now()->addDays(1),
                    'kilometraje' => 10100 + ($i * 123),
                    'gasolina' => $nivelComb,
                    'orden_servicio_id' => $os->id,
                ]);

                $recepcionVehicular = RecepcionesVehiculares::create([
                    'orden_servicio_id' => $os->id,
                    'is_ficticia' => false,
                    'cambiar_archivos' => false,
                    'indicaciones_cliente' => 'Revision general y servicio preventivo',
                ]);

                // Interiores (2 = sin daño)
                InterioresRV::create([
                    'recepcion_vehicular_id' => $recepcionVehicular->id,
                    'puerta_interior_frontal' => 19,
                    'puerta_interior_trasera' => 19,
                    'puerta_delantera_frontal' => 19,
                    'puerta_delantera_trasera' => 19,
                    'asiento_interior_frontal' => 19,
                    'asiento_interior_trasera' => 19,
                    'asiento_delantera_frontal' => 19,
                    'asiento_delantera_trasera' => 19,
                    'consola_central' => 19,
                    'claxon' => 19,
                    'tablero' => 19,
                    'quemacocos' => 19,
                    'toldo' => 19,
                    'elevadores_eletricos' => 19,
                    'luces_interiores' => 19,
                    'seguros_eletricos' => 19,
                    'tapetes' => 19,
                    'climatizador' => 19,
                    'radio' => 19,
                    'espejos_retrovizor' => 19,
                ]);

                // Exteriores (19 = sin daño)
                ExterioresRV::create([
                    'recepcion_vehicular_id' => $recepcionVehicular->id,
                    'antena_radio' => 19,
                    'estribos' => 19,
                    'antena_telefono' => 19,
                    'guardafangos' => 19,
                    'antena_cb' => 19,
                    'parabrisas' => 19,
                    'sistema_alarma' => 19,
                    'limpia_parabrisas' => 19,
                    'luces_exteriores' => 19,
                    'espejos_laterales' => 19,
                ]);

                // Inventario
                InventarioRV::create([
                    'recepcion_vehicular_id' => $recepcionVehicular->id,
                    'llanta' => true,
                    'cubreruedas' => true,
                    'cables_corriente' => false,
                    'candado_ruedas' => false,
                    'estuche_herramientas' => true,
                    'gato' => true,
                    'llave_tuercas' => true,
                    'trajeta_circulacion' => true,
                    'triangulo_seguridad' => false,
                    'extinguidor' => true,
                    'placas' => true,
                ]);

                // Condiciones de pintura (false = no aplica/ok)
                CondicionesPinturaRV::create([
                    'recepcion_vehicular_id' => $recepcionVehicular->id,
                    'decolorada' => false,
                    'emblemas_completos' => true,
                    'color_no_igual' => false,
                    'logos' => true,
                    'exeso_rayones' => false,
                    'exeso_rociado' => false,
                    'pequenias_grietas' => false,
                    'danios_granizado' => false,
                    'carroceria_golpes' => false,
                    'lluvia_acido' => false,
                ]);

                // Responsables (usar los cuatro tipos creados en UsuariosTaller por DatosPrueba)
                ResponsablesOrdenServicio::create([
                    'administrador_transporte_id' => 1,
                    'jefe_de_proceso_id' => 2,
                    'trabajador_id' => 3,
                    'tecnico_id' => 4,
                    'orden_servicio_id' => $os->id,
                ]);

                // Replicar las imágenes demo para cada recepción.
                $rutas = RutasArchivo::whereIn('tipo_id', [25, 26, 58])
                    ->where('estatus_id', 21)
                    ->get()
                    ->keyBy('tipo_id');

                foreach ([25, 26, 58] as $tipoId) {
                    if (! $rutas->has($tipoId)) {
                        throw new \RuntimeException("No existe una ruta de archivo para el tipo {$tipoId}.");
                    }
                }

                $archivosDemo = [
                    [
                        'origen' => public_path('carro.jpeg'),
                        'nombre' => $os->orden_servicio.'_carro_detalles.jpeg',
                        'tipo_id' => 26,
                    ],
                    [
                        'origen' => public_path('firma.jpeg'),
                        'nombre' => $os->orden_servicio.'_firma.jpeg',
                        'tipo_id' => 25,
                    ],
                ];

                for ($evidencia = 1; $evidencia <= 6; $evidencia++) {
                    $archivosDemo[] = [
                        'origen' => public_path('evidencia.png'),
                        'nombre' => $os->orden_servicio.'_evidencia_'.$evidencia.'.png',
                        'tipo_id' => 58,
                    ];
                }

                foreach ($archivosDemo as $archivoDemo) {
                    if (! is_file($archivoDemo['origen'])) {
                        throw new \RuntimeException("No existe la imagen demo: {$archivoDemo['origen']}");
                    }

                    $ruta = $rutas->get($archivoDemo['tipo_id']);
                    $disk = $ruta->disk ?? 'public';
                    $folder = $ruta->folder ?? 'desconocidos';
                    $destino = $folder.'/'.$archivoDemo['nombre'];

                    Storage::disk($disk)->makeDirectory($folder);

                    if (! Storage::disk($disk)->put($destino, file_get_contents($archivoDemo['origen']))) {
                        throw new \RuntimeException("No se pudo copiar la imagen demo a {$destino}.");
                    }

                    Archivos::create([
                        'nombre' => $archivoDemo['nombre'],
                        'recepcion_vehicular_id' => $recepcionVehicular->id,
                        'tipo_id' => $archivoDemo['tipo_id'],
                        'estatus_id' => 21,
                    ]);
                }
            }
        });
    }
}
