<?php

namespace App\Http\Controllers;

use App\Models\CategoriasConceptosDisponibles;
use App\Models\Colores;
use App\Models\ConceptosPerPresupuesto;
use App\Models\ConceptosPresupuestos;
use App\Models\CostosConceptosPresupuestos;
use App\Models\DatosEntrada;
use App\Models\Estatus;
use App\Models\Modelos;
use App\Models\ModuloOrdenesServicio;
use App\Models\OrdenesServicio;
use App\Models\Presupuestos;
use App\Models\RecepcionesVehiculares;
use App\Models\ResponsablesOrdenServicio;
use App\Models\Ubicaciones;
use App\Models\UsuariosTaller;
use App\Models\Vehiculos;
use App\Models\VehiculosConceptosDisponibles;
use App\Rules\ExistTipo;
use App\Rules\TipoCategoriaRule;
use App\Services\AlcanceOrdenesServicio;
use App\Services\FlujoEstatusPresupuesto;
use App\Services\OrdenServicio\FunctionsOrdenServicio;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;

class PresupuestosController extends Controller
{
    public function view(Request $request)
    {
        return Inertia::render('Cortana/Presupuestos');
    }

    public function read(Request $request)
    {
        $validated = $request->validate([
            'currentPage' => ['nullable', 'integer', 'min:1'],
            'itemsPerPage' => ['nullable', 'integer', 'min:1', 'max:200'],
            'search' => ['nullable', 'string', 'max:255'],
            'estatus' => ['nullable', 'array'],
            'estatus.*' => [
                'integer',
                Rule::exists('estatus', 'id')->where('categoria_id', 2),
            ],
            'modulos' => ['nullable', 'array'],
            'modulos.*' => ['integer', 'exists:modulo_ordenes_servicios,id'],
            'empresa_id' => ['nullable', 'integer', 'exists:empresas,id'],
            'usuario_asignado' => [
                'nullable',
                Rule::when(
                    $request->input('usuario_asignado') !== 'sin_usuario',
                    ['integer', 'exists:users,id']
                ),
            ],
            'fechas' => ['nullable', 'array', 'size:2'],
            'fechas.*' => ['date'],
            'orderBy.key' => ['nullable', Rule::in(['folio', 'orden', 'empresa', 'creacion'])],
            'orderBy.order' => ['nullable', Rule::in(['asc', 'desc'])],
        ]);

        $currentPage = (int) ($validated['currentPage'] ?? 1);
        $itemsPerPage = (int) ($validated['itemsPerPage'] ?? 10);
        $search = trim($validated['search'] ?? '');
        $user = $request->user();

        $query = Presupuestos::query()
            ->select('presupuestos.*')
            ->join('ordenes_servicio as orden', 'orden.id', '=', 'presupuestos.orden_servicio_id')
            ->leftJoin('empresas as empresa', 'empresa.id', '=', 'orden.empresa_id')
            ->leftJoin('vehiculos as vehiculo', 'vehiculo.id', '=', 'orden.vehiculo_id')
            ->whereNull('orden.deleted_at')
            ->with([
                'orden_servicio.empresa',
                'orden_servicio.modulo_ordenes_servicio',
                'orden_servicio.usuario_asignado',
                'orden_servicio.vehiculo',
                'estatus',
            ]);
        AlcanceOrdenesServicio::aplicar($query, $user);

        if ($search !== '') {
            $query->where(function ($filter) use ($search) {
                $filter
                    ->where('presupuestos.folio', 'like', "%{$search}%")
                    ->orWhere('orden.orden_servicio', 'like', "%{$search}%")
                    ->orWhere('orden.orden_seguimiento', 'like', "%{$search}%")
                    ->orWhere('vehiculo.economico', 'like', "%{$search}%")
                    ->orWhere('vehiculo.placas', 'like', "%{$search}%")
                    ->orWhere('vehiculo.vin', 'like', "%{$search}%");
            });
        }

        if (($validated['estatus'] ?? []) !== []) {
            $query->whereIn('presupuestos.estatus_id', $validated['estatus']);
        }

        if (($validated['modulos'] ?? []) !== []) {
            $query->whereIn(
                'orden.modulo_orden_id',
                $validated['modulos']
            );
        }

        if (isset($validated['empresa_id'])) {
            $query->where('orden.empresa_id', $validated['empresa_id']);
        }

        if (isset($validated['usuario_asignado'])) {
            abort_unless(
                $user->can('ver_ordenes_servicio_todos'),
                403
            );
            if ($validated['usuario_asignado'] === 'sin_usuario') {
                $query->whereNull('orden.user_asignado');
            } else {
                $query->where(
                    'orden.user_asignado',
                    $validated['usuario_asignado']
                );
            }
        }

        if (isset($validated['fechas'])) {
            $query->whereBetween('presupuestos.created_at', [
                Carbon::parse($validated['fechas'][0])->startOfDay(),
                Carbon::parse($validated['fechas'][1])->endOfDay(),
            ]);
        }

        $orderColumns = [
            'folio' => 'presupuestos.folio',
            'orden' => 'orden.orden_servicio',
            'empresa' => 'empresa.nombre',
            'creacion' => 'presupuestos.created_at',
        ];
        $orderKey = $validated['orderBy']['key'] ?? 'creacion';
        $orderDirection = $validated['orderBy']['order'] ?? 'desc';

        $paginator = $query
            ->orderBy($orderColumns[$orderKey], $orderDirection)
            ->paginate($itemsPerPage, ['*'], 'page', $currentPage);

        $items = $paginator->getCollection()
            ->map(function (Presupuestos $presupuesto) {
                $order = $presupuesto->orden_servicio;
                $vehicle = $order?->vehiculo;

                return [
                    'id' => $presupuesto->id,
                    'orden_id' => $order?->id,
                    'folio' => $presupuesto->folio,
                    'orden' => $order?->orden_servicio ?? '',
                    'empresa' => $order?->empresa?->nombre ?? '',
                    'economico' => $vehicle?->economico ?? '',
                    'placas' => $vehicle?->placas ?? '',
                    'vin' => $vehicle?->vin ?? '',
                    'creacion' => $presupuesto->created_at?->format('d/m/Y H:i'),
                    'modulo_id' => $order?->modulo_orden_id,
                    'modulo' => $order?->modulo_ordenes_servicio?->descripcion ?? '',
                    'user_asignado' => $order?->user_asignado,
                    'usuario_asignado' => $order?->usuario_asignado
                        ? str_replace('-', ' ', $order->usuario_asignado->name)
                        : 'Sin asignar',
                    'estatus_id' => $presupuesto->estatus_id,
                    'estatus' => $presupuesto->estatus?->descripcion ?? '',
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

    public function data(Request $request)
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
                    'vehiculo.color',
                    'vehiculo_concepto',
                    'empresa',
                    'cliente',
                    'ubicacion',
                    'recepcion_vehicular',
                ])->first();
            if ($data) {
                if (! AlcanceOrdenesServicio::puedeAccederOrden($user, $data)) {
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
                    'kilometraje' => $data->entrada->kilometraje,
                    'estimacion' => $data->entrada->estimacion->format('Y-m-d\TH:i'),
                    'administrador' => $data->responsables->administrador_transporte->nombre,
                    'jefe' => $data->responsables->jefe_de_proceso->nombre,
                    'trabajador' => $data->responsables->trabajador->nombre,
                    'tecnico' => $data->responsables->tecnico->nombre,
                    'descripcion_mo' => $data->fallas_reportadas,
                    'indicaciones_cliente' => $data->recepcion_vehicular->indicaciones_cliente ?? '',
                    'garantia' => 'LO ESTIPULADO EN EL CONTRATO',
                    'observaciones' => 'DE ACUERDO A LO DIFICIL DE LA FALLA PARA SU REPARACION',
                    'tipo_id' => $data->presupuestos()
                        ->latest('id')
                        ->value('tipo_id') ?? 7,
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
                    'vehiculo' => [
                        'economico' => $data->vehiculo->economico,
                        'placas' => $data->vehiculo->placas,
                        'vin' => $data->vehiculo->vin,
                        'año' => (string) $data->vehiculo->año,
                        'tipo_id' => $data->vehiculo->tipo_id !== null
                            ? (int) $data->vehiculo->tipo_id
                            : null,
                        'color' => $data->vehiculo->color?->descripcion ?? '',
                        'marca' => [
                            'value' => $data->vehiculo->modelo->marca->id,
                            'label' => $data->vehiculo->modelo->marca->descripcion,
                        ],
                        'modelo' => [
                            'value' => $data->vehiculo->modelo->descripcion,
                            'label' => $data->vehiculo->modelo->descripcion,
                        ],
                        'motor' => [
                            'value' => $data->vehiculo->modelo->motor->id,
                            'label' => $data->vehiculo->modelo->motor->descripcion,
                        ],
                    ],
                ]);
            }

            return response()->json(null);
        }
    }

    public function create(Request $request)
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
            'color' => ['required', 'string', 'max:100'],
            'vehiculo_tipo_id' => ['required', new TipoCategoriaRule(3)],
            'marca_id' => ['required', 'exists:marcas,id'],
            'motor_id' => ['required', 'exists:motores,id'],
            'modelo' => ['required', 'string', 'max:50'],
            'año' => ['required', 'integer', 'min:1900', 'max:2100'],
            // 'vigencia'=>['nullable','date'],
            'modulo_orden' => ['required', 'integer', 'exists:modulo_ordenes_servicios,id'],
        ]);

        $validator->after(function ($validator) use ($request, $user) {
            $modulosPermitidos = $user->modulos_orden->pluck('modulo_orden_id')->toArray();
            if (
                $request->filled('modulo_orden')
                && ! in_array($request->modulo_orden, $modulosPermitidos)
                && ! $user->hasRole('Super Admin')
            ) {
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
            if (! $user->taller_id) {
                $validator->errors()->add(
                    'taller_id',
                    'El usuario debe tener un taller asignado.'
                );
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
        if (
            $ordenservicio
            && ! AlcanceOrdenesServicio::puedeAccederOrden($user, $ordenservicio)
        ) {
            abort(403);
        }

        if (! $ordenservicio) {
            $color = Colores::firstOrCreate([
                'descripcion' => $this->normalizeDescription($request->color),
            ]);
            $modelo = Modelos::withTrashed()->firstOrCreate([
                'descripcion' => $this->normalizeDescription($request->modelo),
                'marca_id' => $request->marca_id,
                'motor_id' => $request->motor_id,
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
                'tipo_id' => $request->vehiculo_tipo_id,
                'color_id' => $color->id,
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
            $ordenservicio->taller_id = $user->taller_id;
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
            $entrada->fecha = Carbon::now();
            $entrada->gasolina = $request->gasolina;
            $entrada->kilometraje = $request->kilometraje;
            $entrada->estimacion = $request->estimacion;
            $entrada->save();

            $responsables = new ResponsablesOrdenServicio;
            $responsables->orden_servicio_id = $ordenservicio->id;
            $responsables->administrador_transporte_id = UsuariosTaller::firstOrCreate(['nombre' => $request->administrador, 'tipo_id' => 1])->id;
            $responsables->jefe_de_proceso_id = UsuariosTaller::firstOrCreate(['nombre' => $request->jefe, 'tipo_id' => 2])->id;
            $responsables->trabajador_id = UsuariosTaller::firstOrCreate(['nombre' => $request->trabajador, 'tipo_id' => 3])->id;
            $responsables->tecnico_id = UsuariosTaller::firstOrCreate(['nombre' => $request->tecnico, 'tipo_id' => 4])->id;
            $responsables->save();

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

        $initialStatus = Estatus::query()
            ->where('categoria_id', 2)
            ->where('descripcion', 'Pendiente De Enviar')
            ->firstOrFail();
        $folio = $request->filled('folio')
            ? $request->folio
            : (new FunctionsOrdenServicio)->GetFolio(
                (string) $ordenservicio->id,
                $ordenservicio->orden_servicio,
                (int) $request->tipo_id
            );
        $presupuesto = Presupuestos::create([
            'orden_servicio_id' => $ordenservicio->id,
            'observaciones' => $request->observaciones,
            'descripcion_mo' => $request->descripcion_mo,
            'garantia' => $request->garantia,
            'folio' => $folio,
            'vigencia' => null,
            'factura_id' => null,
            'tipo_id' => $request->tipo_id,
            'estatus_id' => $initialStatus->id,
        ]);

        return response()->json([
            'message' => 'Presupuesto creado exitosamente',
            'id' => $presupuesto->id,
            'orden_servicio' => $ordenservicio->orden_servicio,
        ], 201);
    }

    public function show(Request $request, Presupuestos $presupuesto): JsonResponse
    {
        $this->ensureVisible($request, $presupuesto);
        $presupuesto->load([
            'orden_servicio.modulo_ordenes_servicio',
            'orden_servicio.vehiculo_concepto',
            'orden_servicio.empresa',
            'orden_servicio.cliente',
            'orden_servicio.ubicacion',
            'orden_servicio.entrada',
            'orden_servicio.responsables.administrador_transporte',
            'orden_servicio.responsables.jefe_de_proceso',
            'orden_servicio.responsables.trabajador',
            'orden_servicio.responsables.tecnico',
            'orden_servicio.recepcion_vehicular',
            'orden_servicio.vehiculo.color',
            'orden_servicio.vehiculo.modelo.marca',
            'orden_servicio.vehiculo.modelo.motor',
            'conceptos_presupuesto.concepto_presupuesto.tipo',
        ]);
        $order = $presupuesto->orden_servicio;
        $vehicle = $order?->vehiculo;
        $model = $vehicle?->modelo;
        $canViewSale = $request->user()->can('ver_venta_presupuestos');

        return response()->json([
            'presupuesto' => [
                'id' => $presupuesto->id,
                'orden_servicio' => $order?->orden_servicio ?? '',
                'folio' => $presupuesto->folio,
                'orden_seguimiento' => $order?->orden_seguimiento ?? '',
                'ubicacion' => $order?->ubicacion?->nombre ?? '',
                'telefono' => $order?->telefono,
                'empresa_id' => $order?->empresa_id,
                'cliente_id' => $order?->cliente_id,
                'gasolina' => $order?->entrada?->gasolina ?? '',
                'kilometraje' => $order?->entrada?->kilometraje,
                'estimacion' => $order?->entrada?->estimacion?->toIso8601String(),
                'administrador' => $order?->responsables?->administrador_transporte?->nombre ?? '',
                'jefe' => $order?->responsables?->jefe_de_proceso?->nombre ?? '',
                'trabajador' => $order?->responsables?->trabajador?->nombre ?? '',
                'tecnico' => $order?->responsables?->tecnico?->nombre ?? '',
                'descripcion_mo' => $presupuesto->descripcion_mo,
                'indicaciones_cliente' => $order?->recepcion_vehicular?->indicaciones_cliente ?? '',
                'garantia' => $presupuesto->garantia,
                'observaciones' => $presupuesto->observaciones,
                'tipo_id' => $presupuesto->tipo_id,
                'vigencia' => $presupuesto->vigencia?->format('Y-m-d'),
                'vehiculo_concepto_id' => $order?->vehiculo_concepto_id,
                'economico' => $vehicle?->economico ?? '',
                'placas' => $vehicle?->placas ?? '',
                'vin' => $vehicle?->vin ?? '',
                'color' => $vehicle?->color?->descripcion ?? '',
                'vehiculo_tipo_id' => $vehicle?->tipo_id !== null
                    ? (int) $vehicle->tipo_id
                    : null,
                'marca_id' => $model?->marca_id,
                'motor_id' => $model?->motor_id,
                'marca' => $model?->marca?->descripcion ?? '',
                'modelo' => $model?->descripcion ?? '',
                'motor' => $model?->motor?->descripcion ?? '',
                'año' => $vehicle?->año,
                'modulo_orden' => $order?->modulo_orden_id ?? '',
            ],
            'orden_servicio' => $order
                ? [
                    'value' => $order->orden_servicio,
                    'label' => $order->orden_servicio,
                ]
                : null,
            'empresa' => $order?->empresa
                ? [
                    'value' => $order->empresa->id,
                    'label' => $order->empresa->nombre,
                ]
                : null,
            'cliente' => $order?->cliente
                ? [
                    'value' => $order->cliente->id,
                    'label' => $order->cliente->nombre,
                ]
                : null,
            'vehiculo_concepto' => $order?->vehiculo_concepto
                ? [
                    'value' => $order->vehiculo_concepto->id,
                    'label' => $order->vehiculo_concepto->descripcion,
                ]
                : null,
            'modulo' => $order?->modulo_ordenes_servicio
                ? [
                    'value' => $order->modulo_ordenes_servicio->id,
                    'label' => $order->modulo_ordenes_servicio->descripcion,
                ]
                : null,
            'vehiculo' => [
                'id' => $vehicle?->id,
                'economico' => $vehicle?->economico ?? '',
                'placas' => $vehicle?->placas ?? '',
                'vin' => $vehicle?->vin ?? '',
                'año' => (string) ($vehicle?->año ?? ''),
                'tipo_id' => $vehicle?->tipo_id !== null
                    ? (int) $vehicle->tipo_id
                    : null,
                'color' => $vehicle?->color?->descripcion ?? '',
                'marca' => $model?->marca
                    ? [
                        'value' => $model->marca->id,
                        'label' => $model->marca->descripcion,
                    ]
                    : null,
                'modelo' => $model
                    ? [
                        'value' => $model->descripcion,
                        'label' => $model->descripcion,
                    ]
                    : null,
                'motor' => $model?->motor
                    ? [
                        'value' => $model->motor->id,
                        'label' => $model->motor->descripcion,
                    ]
                    : null,
            ],
            'conceptos' => $presupuesto->conceptos_presupuesto
                ->map(fn (ConceptosPerPresupuesto $item) => [
                    'id' => $item->id,
                    'concepto_id' => $item->concepto_presupuesto_id,
                    'descripcion' => $item->concepto_presupuesto?->descripcion ?? '',
                    'categoria' => $item->concepto_presupuesto?->tipo?->descripcion ?? '',
                    'cantidad' => $item->cantidad,
                    'costo' => $item->costo,
                    'venta' => $canViewSale ? $item->venta : null,
                    'subtotal' => (float) $item->cantidad * (float) $item->venta,
                ])
                ->values(),
        ]);
    }

    public function update(
        Request $request,
        Presupuestos $presupuesto
    ): JsonResponse {
        $this->ensureVisible($request, $presupuesto);
        $presupuesto->loadMissing('orden_servicio.vehiculo');
        $order = $presupuesto->orden_servicio;
        $vehicle = $order?->vehiculo;

        abort_unless($order && $vehicle, 422, 'El presupuesto no tiene una orden y vehículo válidos.');

        // Estimación, tipo de presupuesto y módulo son inmutables desde esta edición.
        // Al no validarlos ni incluirlos en las actualizaciones, cualquier valor enviado se ignora.
        $validated = $request->validate([
            'folio' => ['nullable', 'string', 'max:50'],
            'orden_seguimiento' => [
                'nullable',
                'string',
                'max:20',
                Rule::unique('ordenes_servicio', 'orden_seguimiento')->ignore($order->id),
            ],
            'ubicacion' => ['required', 'string', 'max:100'],
            'telefono' => ['required', 'string', 'max:20'],
            'empresa_id' => ['required', 'integer', 'exists:empresas,id'],
            'cliente_id' => [
                'required',
                'integer',
                Rule::exists('clientes', 'id')
                    ->where('empresa_id', $request->input('empresa_id')),
            ],
            'gasolina' => ['required', 'integer', 'exists:niveles_combustible,id'],
            'kilometraje' => ['required', 'integer', 'min:0'],
            'administrador' => ['required', 'string', 'max:100'],
            'jefe' => ['required', 'string', 'max:100'],
            'trabajador' => ['required', 'string', 'max:100'],
            'tecnico' => ['required', 'string', 'max:100'],
            'indicaciones_cliente' => ['required', 'string'],
            'vehiculo_concepto_id' => [
                'required',
                'integer',
                Rule::exists('vehiculos_conceptos_disponibles', 'vehiculo_concepto_id')
                    ->where('modulo_orden_id', $order->modulo_orden_id)
                    ->whereNull('deleted_at'),
            ],
            'economico' => [
                'required',
                'string',
                'max:20',
                Rule::unique('vehiculos', 'economico')->ignore($vehicle->id),
            ],
            'placas' => [
                'required',
                'string',
                'max:20',
                Rule::unique('vehiculos', 'placas')->ignore($vehicle->id),
            ],
            'vin' => [
                'required',
                'string',
                'max:50',
                Rule::unique('vehiculos', 'vin')->ignore($vehicle->id),
            ],
            'color' => ['required', 'string', 'max:100'],
            'vehiculo_tipo_id' => ['required', new TipoCategoriaRule(3)],
            'marca_id' => ['required', 'integer', 'exists:marcas,id'],
            'motor_id' => ['required', 'integer', 'exists:motores,id'],
            'modelo' => ['required', 'string', 'max:100'],
            'año' => [
                'required',
                'integer',
                'min:1899',
                'max:'.(date('Y') + 1),
            ],
            'vigencia' => ['nullable', 'date'],
            'garantia' => ['required', 'string'],
            'observaciones' => ['required', 'string'],
            'descripcion_mo' => ['required', 'string'],
        ]);

        DB::transaction(function () use ($validated, $presupuesto, $order, $vehicle) {
            $color = Colores::firstOrCreate([
                'descripcion' => $this->normalizeDescription($validated['color']),
            ]);
            $model = Modelos::withTrashed()->firstOrCreate([
                'descripcion' => $this->normalizeDescription($validated['modelo']),
                'marca_id' => $validated['marca_id'],
                'motor_id' => $validated['motor_id'],
            ]);
            if ($model->trashed()) {
                $model->restore();
            }

            $vehicle->update([
                'economico' => $validated['economico'],
                'placas' => $validated['placas'],
                'vin' => $validated['vin'],
                'año' => $validated['año'],
                'tipo_id' => $validated['vehiculo_tipo_id'],
                'color_id' => $color->id,
                'modelo_id' => $model->id,
            ]);

            $location = Ubicaciones::firstOrCreate([
                'nombre' => strtoupper(trim($validated['ubicacion'])),
            ]);
            $order->update([
                'orden_seguimiento' => $validated['orden_seguimiento']
                    ?: $order->orden_servicio,
                'ubicacion_id' => $location->id,
                'telefono' => $validated['telefono'],
                'empresa_id' => $validated['empresa_id'],
                'cliente_id' => $validated['cliente_id'],
                'vehiculo_concepto_id' => $validated['vehiculo_concepto_id'],
                'fallas_reportadas' => $validated['descripcion_mo'],
                'notas_retraso' => $validated['observaciones'],
            ]);

            $order->entrada?->update([
                'gasolina' => $validated['gasolina'],
                'kilometraje' => $validated['kilometraje'],
            ]);

            ResponsablesOrdenServicio::updateOrCreate(
                ['orden_servicio_id' => $order->id],
                [
                    'administrador_transporte_id' => UsuariosTaller::firstOrCreate([
                        'nombre' => $this->normalizeDescription($validated['administrador']),
                        'tipo_id' => 1,
                    ])->id,
                    'jefe_de_proceso_id' => UsuariosTaller::firstOrCreate([
                        'nombre' => $this->normalizeDescription($validated['jefe']),
                        'tipo_id' => 2,
                    ])->id,
                    'trabajador_id' => UsuariosTaller::firstOrCreate([
                        'nombre' => $this->normalizeDescription($validated['trabajador']),
                        'tipo_id' => 3,
                    ])->id,
                    'tecnico_id' => UsuariosTaller::firstOrCreate([
                        'nombre' => $this->normalizeDescription($validated['tecnico']),
                        'tipo_id' => 4,
                    ])->id,
                ]
            );

            RecepcionesVehiculares::updateOrCreate(
                ['orden_servicio_id' => $order->id],
                ['indicaciones_cliente' => $validated['indicaciones_cliente']]
            );

            $presupuesto->update([
                'folio' => $validated['folio'],
                'vigencia' => $validated['vigencia'],
                'garantia' => $validated['garantia'],
                'observaciones' => $validated['observaciones'],
                'descripcion_mo' => $validated['descripcion_mo'],
            ]);
        });

        return response()->json(['message' => 'Presupuesto actualizado correctamente.']);
    }

    public function ActualizarEstatus(
        Request $request,
        Presupuestos $presupuesto
    ): JsonResponse {
        $validated = $request->validate([
            'tipo_accion' => ['required', Rule::in(['next', 'back'])],
        ]);

        return $this->cambiarEstatus(
            $request,
            $presupuesto,
            $validated['tipo_accion']
        );
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

    public function crearConcepto(
        Request $request,
        Presupuestos $presupuesto
    ): JsonResponse {
        $this->ensureVisible($request, $presupuesto);
        $presupuesto->loadMissing('orden_servicio');
        $order = $presupuesto->orden_servicio;

        abort_unless($order?->modulo_orden_id && $order?->vehiculo_concepto_id, 422);

        $validated = $request->validate(
            [
                'numero' => ['required', 'string', 'max:100'],
                'descripcion' => ['required', 'string', 'max:5000'],
                'garantia_dias' => ['nullable', 'integer', 'min:0', 'max:3650'],
                'tipo_id' => [
                    'required',
                    'integer',
                    Rule::exists(
                        'categorias_conceptos_disponibles',
                        'categoria_concepto_id'
                    )->where('tipo_presupuesto_id', $presupuesto->tipo_id),
                ],
                'categoria_sat_id' => ['required', 'integer', 'exists:categorias_sat,id'],
                'unidad_sat_id' => ['required', 'integer', 'exists:unidades_sat,id'],
                'p_refaccion' => ['required', 'numeric', 'min:0', 'max:99999999.99'],
                'p_mano_obra' => ['required', 'numeric', 'min:0', 'max:99999999.99'],
                'p_total' => ['nullable'],
            ],
            [],
            [
                'numero' => 'número',
                'descripcion' => 'descripción',
                'garantia_dias' => 'garantía en días',
                'tipo_id' => 'categoría',
                'categoria_sat_id' => 'categoría SAT',
                'unidad_sat_id' => 'unidad SAT',
                'p_refaccion' => 'precio de refacción',
                'p_mano_obra' => 'precio de mano de obra',
            ]
        );

        $created = DB::transaction(function () use ($validated, $order, $presupuesto, $request) {
            $availability = VehiculosConceptosDisponibles::withTrashed()
                ->firstOrNew([
                    'vehiculo_concepto_id' => $order->vehiculo_concepto_id,
                    'modulo_orden_id' => $order->modulo_orden_id,
                ]);
            $availability->deleted_at = null;
            $availability->save();

            $concepto = ConceptosPresupuestos::create([
                'num' => $validated['numero'],
                'descripcion' => $validated['descripcion'],
                'garantia_dias' => $validated['garantia_dias'] ?? null,
                'fijo' => false,
                'tipo_id' => $validated['tipo_id'],
                'modulo_orden_servicio_id' => $order->modulo_orden_id,
                'categoria_sat_id' => $validated['categoria_sat_id'],
                'unidad_sat_id' => $validated['unidad_sat_id'],
            ]);
            $total = $validated['p_refaccion'] + $validated['p_mano_obra'];

            $cost = CostosConceptosPresupuestos::create([
                'concepto_presupuesto_id' => $concepto->id,
                'vehiculo_concepto_id' => $order->vehiculo_concepto_id,
                'usuario_id' => $request->user()->id,
                'p_refaccion' => $validated['p_refaccion'],
                'p_mano_obra' => $validated['p_mano_obra'],
                'p_total' => $total,
            ]);

            $item = ConceptosPerPresupuesto::create([
                'cantidad' => 1,
                'costo' => $total,
                'venta' => $total,
                'presupuesto_id' => $presupuesto->id,
                'concepto_presupuesto_id' => $concepto->id,
                'user_id' => $request->user()->id,
            ]);

            return [
                'concepto_id' => $concepto->id,
                'costo_id' => $cost->id,
                'concepto_presupuesto_id' => $item->id,
            ];
        });

        return response()->json([
            'message' => 'Concepto creado y agregado al presupuesto correctamente.',
            ...$created,
        ], 201);
    }

    public function actualizarConceptos(
        Request $request,
        Presupuestos $presupuesto
    ): JsonResponse {
        $this->ensureVisible($request, $presupuesto);
        $canViewSale = $request->user()->can('ver_venta_presupuestos');
        $validated = $request->validate([
            'conceptos' => ['required', 'array', 'min:1'],
            'conceptos.*.id' => [
                'required',
                'integer',
                'distinct',
                'exists:conceptos_per_presupuestos,id',
            ],
            'conceptos.*.cantidad' => ['required', 'integer', 'min:1', 'max:99999999'],
            'conceptos.*.costo' => [
                'required',
                'numeric',
                'min:0',
                'max:99999999.50',
                'multiple_of:0.5',
            ],
            'conceptos.*.venta' => $canViewSale
                ? [
                    'required',
                    'numeric',
                    'min:0',
                    'max:99999999.50',
                    'multiple_of:0.5',
                ]
                : ['prohibited'],
        ]);
        $items = ConceptosPerPresupuesto::query()
            ->where('presupuesto_id', $presupuesto->id)
            ->whereIn('id', collect($validated['conceptos'])->pluck('id'))
            ->get()
            ->keyBy('id');

        if ($items->count() !== count($validated['conceptos'])) {
            return response()->json([
                'message' => 'Uno o más conceptos no pertenecen a este presupuesto.',
            ], 422);
        }

        DB::transaction(function () use ($validated, $items) {
            foreach ($validated['conceptos'] as $values) {
                $update = [
                    'cantidad' => $values['cantidad'],
                    'costo' => $values['costo'],
                ];

                if (array_key_exists('venta', $values)) {
                    $update['venta'] = $values['venta'];
                }

                $items[$values['id']]->update($update);
            }
        });

        return response()->json([
            'message' => 'Cantidades y precios del presupuesto actualizados correctamente.',
            'actualizados' => count($validated['conceptos']),
        ]);
    }

    private function ensureVisible(Request $request, Presupuestos $presupuesto): void
    {
        abort_unless(
            AlcanceOrdenesServicio::puedeAcceder(
                $request->user(),
                $presupuesto
            ),
            403
        );
    }

    private function cambiarEstatus(
        Request $request,
        Presupuestos $presupuesto,
        string $direction
    ): JsonResponse {
        return DB::transaction(function () use ($request, $presupuesto, $direction) {
            $current = Presupuestos::query()
                ->lockForUpdate()
                ->findOrFail($presupuesto->id);
            $current->loadMissing(['orden_servicio', 'estatus']);
            $this->ensureVisible($request, $current);

            $action = FlujoEstatusPresupuesto::accion(
                $current->estatus?->descripcion,
                $direction
            );

            if (! $action) {
                throw ValidationException::withMessages([
                    'estatus' => 'El presupuesto no permite esta transición desde su estado actual.',
                ]);
            }

            abort_unless(
                $request->user()->can($action['permiso']),
                403,
                'No tienes permiso para realizar esta acción del presupuesto.'
            );

            $target = Estatus::query()
                ->where('categoria_id', 2)
                ->where('descripcion', $action['destino'])
                ->first();

            if (! $target) {
                throw ValidationException::withMessages([
                    'estatus' => 'No se encontró el estado de destino configurado.',
                ]);
            }

            $current->update([
                'estatus_id' => $target->id,
            ]);

            return response()->json([
                'message' => 'Estado actualizado a '.$target->descripcion.'.',
                'estatus' => [
                    'value' => $target->id,
                    'label' => $target->descripcion,
                ],
            ]);
        });
    }

    private function normalizeDescription(string $description): string
    {
        return mb_strtolower(trim($description), 'UTF-8');
    }
}
