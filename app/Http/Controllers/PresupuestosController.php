<?php

namespace App\Http\Controllers;

use App\Models\CategoriasConceptosDisponibles;
use App\Models\ConceptosPerPresupuesto;
use App\Models\ConceptosPresupuestos;
use App\Models\DatosEntrada;
use App\Models\Marcas;
use App\Models\Modelos;
use App\Models\ModuloOrdenesServicio;
use App\Models\Motores;
use App\Models\OrdenesServicio;
use App\Models\Presupuestos;
use App\Models\RecepcionesVehiculares;
use App\Models\ResponsablesOrdenServicio;
use App\Models\Ubicaciones;
use App\Models\UsuariosTaller;
use App\Models\Vehiculos;
use App\Rules\ExistTipo;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class PresupuestosController extends Controller
{
    public function GetDataPerOrdenServicio(Request $request)
    {
        $user = Auth::user()->load('modulos_orden');
        if ($request->filled('orden_servicio')) {
            $orden = $request->orden_servicio;
            $data = OrdenesServicio::where('orden_servicio', $orden)
                ->with([
                    'entrada',
                    'responsables.administrador_transporte',
                    'responsables.jefe_de_proceso',
                    'responsables.trabajador',
                    'responsables.tecnico',
                    'vehiculo.modelo.marca',
                    'vehiculo.modelo.motor',
                    'vehiculo_concepto',
                    'empresa',
                    'cliente',
                    'ubicacion',
                    'recepcion_vehicular',
                ])->first();
            if ($data) {
                if (! in_array($data->modulo_orden_id, $user->modulos_orden->pluck('modulo_orden_id')->toarray())) {
                    return response()->json(null);
                }
                $presupuesto = [
                    'orden_servicio' => $data->orden_servicio,
                    'folio' => '',
                    'orden_seguimiento' => '',
                    'ubicacion' => $data->ubicacion->nombre,
                    'telefono' => $data->telefono,
                    'empresa_id' => $data->empresa_id,
                    'cliente_id' => $data->cliente_id,
                    'gasolina' => $data->entrada->gasolina,
                    'kilometraje' => $data->entrada->kilomentraje,
                    'estimacion' => $data->entrada->estimacion->format('Y-m-d\TH:i'),
                    'administrador' => $data->responsables->administrador_transporte->nombre,
                    'jefe' => $data->responsables->jefe_de_proceso->nombre,
                    'trabajador' => $data->responsables->trabajador->nombre,
                    'tecnico' => $data->responsables->tecnico->nombre,
                    'descripcion_mo' => $data->fallas_reportadas,
                    'indicaciones_cliente' => $data->recepcion_vehicular->indicaciones_cliente ?? '',
                    'garantia' => 'LO ESTIPULADO EN EL CONTRATO',
                    'observaciones' => 'DE ACUERDO A LO DIFICIL DE LA FALLA PARA SU REPARACION',
                    'tipo_id' => 3,
                    'vehiculo_concepto_id' => $data->vehiculo_concepto_id,
                    'economico' => $data->vehiculo->economico,
                    'placas' => $data->vehiculo->placas,
                    'vin' => $data->vehiculo->vin,
                    'marca' => $data->vehiculo->modelo->marca->descripcion,
                    'modelo' => $data->vehiculo->modelo->descripcion,
                    'motor' => $data->vehiculo->modelo->motor->descripcion,
                    'año' => $data->vehiculo->año,
                    'vigencia' => null,
                    'modulo_orden' => $data->modulo_orden_id,
                ];

                return response()->json([
                    'presupuesto' => $presupuesto,
                    'empresa' => ['value' => $data->empresa->id, 'label' => $data->empresa->nombre],
                    'cliente' => ['value' => $data->cliente->id, 'label' => $data->cliente->nombre],
                    'vehiculo_concepto' => ['value' => $data->vehiculo_concepto->id, 'label' => $data->vehiculo_concepto->descripcion],
                ]);
            }

            return response()->json(null);
        }
    }

    public function CreatePresupuesto(Request $request)
    {
        $user = Auth::user()->load('modulos_orden');
        $validator = Validator::make($request, [
            'orden_servicio' => ['nullable', 'string', 'max:20'],
            'folio' => ['nullable', 'string', 'max:20'],
            'orden_seguimiento' => ['nullable', 'string', 'max:20'],
            'ubicacion' => ['required', 'string', 'max:100'],
            'telefono' => ['required', 'string', 'max:20'],
            'empresa_id' => ['required', 'exists:empresas,id'],
            'cliente_id' => ['required', 'exists:clientes,id'],
            'gasolina' => ['required', 'exists:niveles_combustible,id'],
            'kilometraje' => ['required', 'integer', 'min:0'],
            'estimacion' => ['required', 'date'],
            'administrador' => ['required', 'string', 'max:100'],
            'jefe' => ['required', 'string', 'max:100'],
            'trabajador' => ['required', 'string', 'max:100'],
            'tecnico' => ['required', 'string', 'max:100'],
            'descripcion_mo' => ['required', 'string'],
            'indicaciones_cliente' => ['required', 'string'],
            'garantia' => ['required', 'string'],
            'observaciones' => ['required', 'string'],
            'tipo_id' => ['required', new ExistTipo(2, $request->tipo_id)],
            'vehiculo_concepto_id' => ['required', 'exists:vehiculos_conceptos,id'],
            'economico' => ['required', 'string', 'max:20'],
            'placas' => ['required', 'string', 'max:20'],
            'vin' => ['required', 'string', 'max:50'],
            'marca' => ['required', 'string', 'max:50'],
            'modelo' => ['required', 'string', 'max:50'],
            'motor' => ['required', 'string', 'max:100'],
            'año' => ['required', 'integer', 'min:1900', 'max:2100'],
            // 'vigencia'=>['nullable','date'],
            'modulo_orden' => ['required', 'integer', 'exists:modulo_ordenes_servicios,id'],
        ]);

        $validator->after(function ($validator) use ($request, $user) {
            $modulosPermitidos = $user->modulos_orden->pluck('modulo_orden_id')->toArray();
            if ($request->filled('modulo_orden') && ! in_array($request->modulo_orden, $modulosPermitidos)) {
                $validator->errors()->add('modulo_orden', 'El usuario no tiene permiso para este módulo de orden.');
            }
            if (! Vehiculos::where('economico', $request->economico)->orWhere('placas', $request->placas)->exists()) {
                if (Vehiculos::where('economico', $request->economico)->exists()) {
                    $validator->errors()->add('economico', 'el economico registrado con otras placas');
                }
                if (Vehiculos::where('placas', $request->placas)->exists()) {
                    $validator->errors()->add('placas', 'las placas registradas en otro economico');
                }
            }
        });

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        if ($request->filled('orden_servicio')) {
            $orden = $request->orden_servicio;
        } else {
            $clave = ModuloOrdenesServicio::find($request->modulo_orden)->clave;
            $num = OrdenesServicio::where('modulo_orden_id', $request->modulo_orden)->count();
            do {
                $num++;
                $orden = $clave.str_pad($num, 6, '0', STR_PAD_LEFT);
                $existingOrder = OrdenesServicio::where('orden_servicio', $orden)->exists();
            } while ($existingOrder);
        }

        $ordenservicio = OrdenesServicio::where('orden_servicio', $orden)->first();
        if (! $ordenservicio) {
            $marca = Marcas::firstOrCreate([
                'descripcion' => $this->normalizeDescription($request->marca),
            ]);
            $motor = Motores::withTrashed()->firstOrCreate([
                'descripcion' => $this->normalizeDescription($request->motor),
            ]);
            if ($motor->trashed()) {
                $motor->restore();
            }
            $modelo = Modelos::withTrashed()->firstOrCreate([
                'descripcion' => $this->normalizeDescription($request->modelo),
                'marca_id' => $marca->id,
                'motor_id' => $motor->id,
            ]);
            if ($modelo->trashed()) {
                $modelo->restore();
            }
            $vehiculo = Vehiculos::firstOrCreate([
                'economico' => $request->economico,
                'placas' => $request->placas,
            ], [
                'año' => $request->año,
                'vin' => $request->vin,
                'modelo_id' => $modelo->id,
                'tipo_id' => 1,
                'color_id' => 1,
            ]);
            $ubicacion = Ubicaciones::firstorCreate([
                'nombre' => strtoupper(trim($request->ubicacion)),
            ]);

            $ordenservicio = new OrdenesServicio;
            $ordenservicio->orden_servicio = $orden;
            $ordenservicio->orden_seguimiento = $request->orden_seguimiento ?? $orden;
            $ordenservicio->modulo_orden_id = $request->modulo_orden;
            $ordenservicio->vehiculo_id = $vehiculo->id;
            $ordenservicio->vehiculo_concepto_id = $request->vehiculo_concepto_id;
            $ordenservicio->user_id = $user->id;
            $ordenservicio->empresa_id = $request->empresa_id;
            $ordenservicio->cliente_id = $request->cliente_id;
            $ordenservicio->diagnostico = null;
            $ordenservicio->fallas_reportadas = $request->descripcion_mo;
            $ordenservicio->notas_retraso = $request->observaciones;
            $ordenservicio->telefono = $request->telefono;
            $ordenservicio->ubicacion_id = $ubicacion->id;
            $ordenservicio->save();

            // Crear datos de entrada
            $entrada = new DatosEntrada;
            $entrada->orden_servicio_id = $ordenservicio->id;
            $entrada->gasolina = $request->gasolina;
            $entrada->kilomentraje = $request->kilometraje;
            $entrada->estimacion = $request->estimacion;
            $entrada->save();

            $responsables = new ResponsablesOrdenServicio;
            $responsables->orden_servicio_id = $ordenservicio->id;
            $responsables->administrador_transporte_id = UsuariosTaller::firstOrCreate(['nombre' => $request->administrador, 'tipo_id' => 1])->id;
            $responsables->jefe_de_proceso_id = UsuariosTaller::firstOrCreate(['nombre' => $request->jefe, 'tipo_id' => 1])->id;
            $responsables->trabajador_id = UsuariosTaller::firstOrCreate(['nombre' => $request->trabajador, 'tipo_id' => 1])->id;
            $responsables->tecnico_id = UsuariosTaller::firstOrCreate(['nombre' => $request->tecnico, 'tipo_id' => 1])->id;

            $ExterioresEquipo = new \App\Models\ExterioresRV;
            $EquipoInventario = new \App\Models\InventarioRV;
            $InterioresEquipo = new \App\Models\InterioresRV;
            $CondicionesPintura = new \App\Models\CondicionesPinturaRV;
            $recepcionVehicular = RecepcionesVehiculares::create([
                'orden_servicio_id' => $ordenservicio->id,
                'is_ficticia' => true,
                'cambiar_archivos' => false,
                'indicaciones_cliente' => $request->indicaciones_cliente,
            ]);

            $ExterioresEquipo->recepcion_vehicular_id = $recepcionVehicular->id;
            $ExterioresEquipo->antena_radio = 3;
            $ExterioresEquipo->antena_telefono = 3;
            $ExterioresEquipo->antena_cb = 3;
            $ExterioresEquipo->estribos = 3;
            $ExterioresEquipo->espejos_laterales = 3;
            $ExterioresEquipo->guardafangos = 3;
            $ExterioresEquipo->parabrisas = 3;
            $ExterioresEquipo->sistema_alarma = 3;
            $ExterioresEquipo->limpia_parabrisas = 3;
            $ExterioresEquipo->luces_exteriores = 3;
            $ExterioresEquipo->save();

            $EquipoInventario->recepcion_vehicular_id = $recepcionVehicular->id;
            $EquipoInventario->llanta = 0;
            $EquipoInventario->cubreruedas = 0;
            $EquipoInventario->cables_corriente = 0;
            $EquipoInventario->candado_ruedas = 0;
            $EquipoInventario->estuche_herramientas = 0;
            $EquipoInventario->gato = 0;
            $EquipoInventario->llave_tuercas = 0;
            $EquipoInventario->tarjeta_circulacion = 0;
            $EquipoInventario->triangulo_seguridad = 0;
            $EquipoInventario->extinguidor = 0;
            $EquipoInventario->placas = 0;
            $EquipoInventario->save();

            $InterioresEquipo->recepcion_vehicular_id = $recepcionVehicular->id;
            $InterioresEquipo->puerta_interior_frontal = 3;
            $InterioresEquipo->puerta_interior_trasera = 3;
            $InterioresEquipo->puerta_delantera_frontal = 3;
            $InterioresEquipo->puerta_delantera_trasera = 3;
            $InterioresEquipo->asiento_interior_frontal = 3;
            $InterioresEquipo->asiento_interior_trasera = 3;
            $InterioresEquipo->asiento_delantera_frontal = 3;
            $InterioresEquipo->asiento_delantera_trasera = 3;
            $InterioresEquipo->consola_central = 3;
            $InterioresEquipo->claxon = 3;
            $InterioresEquipo->tablero = 3;
            $InterioresEquipo->quemacocos = 3;
            $InterioresEquipo->toldo = 3;
            $InterioresEquipo->elevadores_eletricos = 3;
            $InterioresEquipo->luces_interiores = 3;
            $InterioresEquipo->seguros_eletricos = 3;
            $InterioresEquipo->tapetes = 3;
            $InterioresEquipo->climatizador = 3;
            $InterioresEquipo->radio = 3;
            $InterioresEquipo->espejos_retrovizor = 3;
            $InterioresEquipo->save();

            $CondicionesPintura->recepcion_vehicular_id = $recepcionVehicular->id;
            $CondicionesPintura->decolorada = 0;
            $CondicionesPintura->emblemas_completos = 0;
            $CondicionesPintura->color_no_igual = 0;
            $CondicionesPintura->logos = 0;
            $CondicionesPintura->exeso_rayones = 0;
            $CondicionesPintura->exeso_rociado = 0;
            $CondicionesPintura->pequenias_grietas = 0;
            $CondicionesPintura->danios_granizado = 0;
            $CondicionesPintura->carroceria_golpes = 0;
            $CondicionesPintura->lluvia_acido = 0;
            $CondicionesPintura->save();

        }

        return response()->json(['message' => 'Presupuesto creado exitosamente', 'orden_servicio' => $ordenservicio->orden_servicio], 201);
    }

    public function show(Request $request, Presupuestos $presupuesto): JsonResponse
    {
        $this->ensureVisible($request, $presupuesto);
        $presupuesto->load([
            'orden_servicio.modulo_ordenes_servicio',
            'orden_servicio.vehiculo_concepto',
            'orden_servicio.empresa',
            'orden_servicio.vehiculo',
            'conceptos_presupuesto.concepto_presupuesto.tipo',
        ]);
        $order = $presupuesto->orden_servicio;

        return response()->json([
            'presupuesto' => [
                'id' => $presupuesto->id,
                'folio' => $presupuesto->folio,
                'tipo_id' => $presupuesto->tipo_id,
                'vigencia' => $presupuesto->vigencia?->format('Y-m-d'),
                'garantia' => $presupuesto->garantia,
                'observaciones' => $presupuesto->observaciones,
                'descripcion_mo' => $presupuesto->descripcion_mo,
                'orden' => $order?->orden_servicio ?? '',
                'modulo' => $order?->modulo_ordenes_servicio?->descripcion ?? '',
                'vehiculo' => $order?->vehiculo_concepto?->descripcion ?? '',
                'empresa' => $order?->empresa?->nombre ?? '',
                'unidad' => trim(
                    ($order?->vehiculo?->economico ?? '').' · '.($order?->vehiculo?->placas ?? ''),
                    ' ·'
                ),
            ],
            'conceptos' => $presupuesto->conceptos_presupuesto
                ->map(fn (ConceptosPerPresupuesto $item) => [
                    'id' => $item->id,
                    'concepto_id' => $item->concepto_presupuesto_id,
                    'descripcion' => $item->concepto_presupuesto?->descripcion ?? '',
                    'categoria' => $item->concepto_presupuesto?->tipo?->descripcion ?? '',
                    'cantidad' => $item->cantidad,
                    'costo' => $item->costo,
                    'venta' => $item->venta,
                ])
                ->values(),
        ]);
    }

    public function update(
        Request $request,
        Presupuestos $presupuesto
    ): JsonResponse {
        $this->ensureVisible($request, $presupuesto);
        $validated = $request->validate([
            'folio' => ['required', 'string', 'max:50'],
            'vigencia' => ['nullable', 'date'],
            'garantia' => ['required', 'string'],
            'observaciones' => ['required', 'string'],
            'descripcion_mo' => ['required', 'string'],
        ]);

        $presupuesto->update($validated);

        return response()->json(['message' => 'Presupuesto actualizado correctamente.']);
    }

    public function destroy(Request $request, Presupuestos $presupuesto): JsonResponse
    {
        $this->ensureVisible($request, $presupuesto);

        DB::transaction(function () use ($presupuesto) {
            $presupuesto->conceptos_presupuesto()->get()->each->delete();
            $presupuesto->delete();
        });

        return response()->json(['message' => 'Presupuesto eliminado correctamente.']);
    }

    public function conceptosDisponibles(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'presupuesto_id' => ['required', 'integer', 'exists:presupuestos,id'],
            'currentPage' => ['nullable', 'integer', 'min:1'],
            'itemsPerPage' => ['nullable', 'integer', 'min:1', 'max:100'],
            'search' => ['nullable', 'string', 'max:255'],
            'categorias' => ['nullable', 'array'],
            'categorias.*' => [
                'integer',
                Rule::exists('tipos', 'id')->where('categoria_id', 7),
            ],
        ]);
        $presupuesto = Presupuestos::findOrFail($validated['presupuesto_id']);
        $this->ensureVisible($request, $presupuesto);
        $presupuesto->loadMissing('orden_servicio');
        $moduleId = $presupuesto->orden_servicio->modulo_orden_id;
        $vehicleId = $presupuesto->orden_servicio->vehiculo_concepto_id;
        $search = trim($validated['search'] ?? '');

        $query = ConceptosPresupuestos::query()
            ->where('modulo_orden_servicio_id', $moduleId)
            ->whereIn(
                'tipo_id',
                CategoriasConceptosDisponibles::query()
                    ->select('categoria_concepto_id')
                    ->where('tipo_presupuesto_id', $presupuesto->tipo_id)
            )
            ->whereDoesntHave(
                'presupuestos_asignados',
                fn ($assigned) => $assigned->where('presupuesto_id', $presupuesto->id)
            )
            ->whereHas('costos')
            ->where(function ($availability) use ($vehicleId) {
                $availability
                    ->where('fijo', false)
                    ->orWhere(function ($fixed) use ($vehicleId) {
                        $fixed
                            ->where('fijo', true)
                            ->whereHas(
                                'costos',
                                fn ($cost) => $cost->where('vehiculo_concepto_id', $vehicleId)
                            );
                    });
            })
            ->with([
                'tipo:id,descripcion',
                'costos.vehiculo_concepto:id,descripcion',
            ]);

        if ($search !== '') {
            $query->where('descripcion', 'like', "%{$search}%");
        }

        if (($validated['categorias'] ?? []) !== []) {
            $query->whereIn('tipo_id', $validated['categorias']);
        }

        $paginator = $query
            ->orderBy('descripcion')
            ->paginate(
                $validated['itemsPerPage'] ?? 10,
                ['*'],
                'page',
                $validated['currentPage'] ?? 1
            );

        $items = $paginator->getCollection()
            ->map(function (ConceptosPresupuestos $concepto) use ($vehicleId) {
                $cost = $concepto->costos
                    ->firstWhere('vehiculo_concepto_id', $vehicleId)
                    ?? $concepto->costos->first();

                return [
                    'id' => $concepto->id,
                    'descripcion' => $concepto->descripcion,
                    'categoria' => $concepto->tipo?->descripcion ?? '',
                    'fijo' => $concepto->fijo,
                    'vehiculo' => $cost?->vehiculo_concepto?->descripcion ?? '',
                    'total' => $cost?->p_total ?? 0,
                ];
            })
            ->values();

        return response()->json([
            'currentPage' => $paginator->currentPage(),
            'itemsPerPage' => $paginator->perPage(),
            'totalPages' => $paginator->lastPage(),
            'totalItems' => $paginator->total(),
            'items' => $items,
        ]);
    }

    public function agregarConceptos(
        Request $request,
        Presupuestos $presupuesto
    ): JsonResponse {
        $this->ensureVisible($request, $presupuesto);
        $validated = $request->validate([
            'conceptos' => ['required', 'array', 'min:1'],
            'conceptos.*' => ['integer', 'distinct', 'exists:conceptos_presupuestos,id'],
        ]);
        $presupuesto->loadMissing('orden_servicio');
        $moduleId = $presupuesto->orden_servicio->modulo_orden_id;
        $vehicleId = $presupuesto->orden_servicio->vehiculo_concepto_id;

        $conceptos = ConceptosPresupuestos::query()
            ->whereIn('id', $validated['conceptos'])
            ->where('modulo_orden_servicio_id', $moduleId)
            ->whereIn(
                'tipo_id',
                CategoriasConceptosDisponibles::query()
                    ->select('categoria_concepto_id')
                    ->where('tipo_presupuesto_id', $presupuesto->tipo_id)
            )
            ->whereHas('costos')
            ->where(function ($availability) use ($vehicleId) {
                $availability
                    ->where('fijo', false)
                    ->orWhere(function ($fixed) use ($vehicleId) {
                        $fixed
                            ->where('fijo', true)
                            ->whereHas(
                                'costos',
                                fn ($cost) => $cost->where('vehiculo_concepto_id', $vehicleId)
                            );
                    });
            })
            ->with('costos')
            ->get();

        if ($conceptos->count() !== count($validated['conceptos'])) {
            return response()->json([
                'message' => 'Uno o más conceptos no están disponibles para este presupuesto.',
            ], 422);
        }

        $added = DB::transaction(function () use ($conceptos, $presupuesto, $request, $vehicleId) {
            $count = 0;

            foreach ($conceptos as $concepto) {
                $cost = $concepto->costos
                    ->firstWhere('vehiculo_concepto_id', $vehicleId)
                    ?? $concepto->costos->first();
                $item = ConceptosPerPresupuesto::withTrashed()
                    ->firstOrNew([
                        'presupuesto_id' => $presupuesto->id,
                        'concepto_presupuesto_id' => $concepto->id,
                    ]);

                if ($item->exists && ! $item->trashed()) {
                    continue;
                }

                $item->fill([
                    'cantidad' => 1,
                    'costo' => $cost->p_total,
                    'venta' => $cost->p_total,
                    'user_id' => $request->user()->id,
                ]);
                $item->deleted_at = null;
                $item->save();
                $count++;
            }

            return $count;
        });

        return response()->json([
            'message' => "{$added} conceptos agregados correctamente.",
            'agregados' => $added,
        ]);
    }

    private function ensureVisible(Request $request, Presupuestos $presupuesto): void
    {
        $presupuesto->loadMissing('orden_servicio');
        abort_unless(
            $presupuesto->orden_servicio
                && (
                    $request->user()->hasRole('Super Admin')
                    || $request->user()
                        ->modulos_orden()
                        ->where('modulo_orden_id', $presupuesto->orden_servicio->modulo_orden_id)
                        ->exists()
                ),
            403
        );
    }

    private function normalizeDescription(string $description): string
    {
        return mb_strtolower(trim($description), 'UTF-8');
    }
}
