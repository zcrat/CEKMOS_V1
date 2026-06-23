<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\User;
use App\Models\OrdenesServicio;
use App\Models\Empresas;
use App\Models\Clientes;
use App\Models\Vehiculos;
use App\Models\VehiculosConceptos;
use App\Models\ModuloOrdenesServicio;
use App\Models\Ubicaciones;
use App\Models\NivelesCombustible;
use App\Models\DatosEntrada;
use App\Models\DatosSalida;
use App\Models\InterioresOrdenServicio;
use App\Models\ExterioresOrdenServicio;
use App\Models\InventarioOrdenServicio;
use App\Models\CondicionesPinturaOrdenServicio;
use App\Models\ResponsablesOrdenServicio;
use App\Models\Archivos;

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
                    'cambiar_archivos' => false,
                    'diagnostico' => Carbon::now()->addHours(rand(1, 8)),
                    'indicaciones_cliente' => 'Revisión general y servicio preventivo',
                    'notas_mecanico' => 'Sin novedades relevantes',
                    'notas_retraso' => null,
                    'telefono' => '443-000-000' . ($i % 10),
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
                    'kilomentraje' => 10100 + ($i * 123),
                    'gasolina' => $nivelComb,
                    'orden_servicio_id' => $os->id,
                ]);

                // Interiores (2 = sin daño)
                InterioresOrdenServicio::create([
                    'orden_servicio_id' => $os->id,
                    'puerta_interior_frontal' => 2,
                    'puerta_interior_trasera' => 2,
                    'puerta_delantera_frontal' => 2,
                    'puerta_delantera_trasera' => 2,
                    'asiento_interior_frontal' => 2,
                    'asiento_interior_trasera' => 2,
                    'asiento_delantera_frontal' => 2,
                    'asiento_delantera_trasera' => 2,
                    'consola_central' => 2,
                    'claxon' => 2,
                    'tablero' => 2,
                    'quemacocos' => 2,
                    'toldo' => 2,
                    'elevadores_eletricos' => 2,
                    'luces_interiores' => 2,
                    'seguros_eletricos' => 2,
                    'tapetes' => 2,
                    'climatizador' => 2,
                    'radio' => 2,
                    'espejos_retrovizor' => 2,
                ]);

                // Exteriores (2 = sin daño)
                ExterioresOrdenServicio::create([
                    'orden_servicio_id' => $os->id,
                    'antena_radio' => 2,
                    'estribos' => 2,
                    'antena_telefono' => 2,
                    'guardafangos' => 2,
                    'antena_cb' => 2,
                    'parabrisas' => 2,
                    'sistema_alarma' => 2,
                    'limpia_parabrisas' => 2,
                    'luces_exteriores' => 2,
                    'espejos_laterales' => 2,
                ]);

                // Inventario
                InventarioOrdenServicio::create([
                    'orden_servicio_id' => $os->id,
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
                CondicionesPinturaOrdenServicio::create([
                    'orden_servicio_id' => $os->id,
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

                // Archivos asociados usando las 3 imágenes esperadas
                Archivos::create([
                    'nombre' => 'firma.jpeg',
                    'orden_servicio_id' => $os->id,
                    'tipo_id' => 25,
                    'estatus_id' => 21,
                ]);

                Archivos::create([
                    'nombre' => 'carro.jpeg',
                    'orden_servicio_id' => $os->id,
                    'tipo_id' => 26,
                    'estatus_id' => 21,
                ]);

                Archivos::create([
                    'nombre' => 'evidencia.jpeg',
                    'orden_servicio_id' => $os->id,
                    'tipo_id' => 63,
                    'estatus_id' => 21,
                ]);
            }
        });
    }
}

