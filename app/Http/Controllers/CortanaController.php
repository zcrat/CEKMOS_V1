<?php

namespace App\Http\Controllers;

use App\Models\OrdenesServicio;
use App\Models\Presupuestos;
use App\Models\RutasArchivo;
use App\Services\AlcanceOrdenesServicio;
use App\Services\FlujoEstatusPresupuesto;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class CortanaController extends Controller
{
    public function PresupuestosVista(Request $request)
    {
        return Inertia::render('Cortana/Presupuestos');
    }

    public function GetItems(Request $request)
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
            ->map(function (Presupuestos $presupuesto) use ($user) {
                $order = $presupuesto->orden_servicio;
                $vehicle = $order?->vehiculo;
                $statusActions = collect(
                    FlujoEstatusPresupuesto::acciones(
                        $presupuesto->estatus?->descripcion
                    )
                )
                    ->filter(
                        fn (array $action) => $user->can($action['permiso'])
                    )
                    ->map(fn (array $action, string $direction) => [
                        'direccion' => $direction,
                        'descripcion' => $action['descripcion'],
                    ])
                    ->values();

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
                    'estatus_id' => $presupuesto->estatus_id,
                    'estatus' => $presupuesto->estatus?->descripcion ?? '',
                    'acciones_estatus' => $statusActions,
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

    public function GetOrdenServicio(Request $request)
    {
        $request->validate([
            'id' => ['required', 'exists:ordenes_servicio,id'],
        ]);
        $ordenservicio = OrdenesServicio::with([
            'recepcion_vehicular.interiores',
            'recepcion_vehicular.exteriores',
            'recepcion_vehicular.inventario',
            'recepcion_vehicular.condiciones_pintura',
            'recepcion_vehicular.archivos',
            'empresa',
            'cliente',
            'vehiculo',
            'vehiculo_concepto',
            'entrada',
            'responsables.administrador_transporte',
            'responsables.jefe_de_proceso',
            'responsables.trabajador',
            'responsables.tecnico',
        ])->findOrFail($request->id);
        abort_unless(
            AlcanceOrdenesServicio::puedeAccederOrden(
                $request->user(),
                $ordenservicio
            ),
            403
        );

        $responsables = $ordenservicio->responsables;
        $recepcionVehicular = $ordenservicio->recepcion_vehicular;
        $condicionespintura = $recepcionVehicular?->condiciones_pintura;
        $inventariobase = $recepcionVehicular?->inventario;
        $interioresbase = $recepcionVehicular?->interiores;
        $exterioresbase = $recepcionVehicular?->exteriores;

        $generales = [
            'id' => $ordenservicio->id,
            'orden_seguimiento' => $ordenservicio->orden_seguimiento,
            'orden_opcional' => $ordenservicio->orden_opcional ?? '',
            'ubicacion' => $ordenservicio->ubicacion->nombre,
            'tipo_id' => $ordenservicio->tipo_id,
            'modulo_orden' => $ordenservicio->modulo_orden_id,
            'vehiculo' => [
                'value' => $ordenservicio->vehiculo_id,
                'label' => $ordenservicio->vehiculo ?
                ($ordenservicio->vehiculo->economico.' - '.$ordenservicio->vehiculo->placas) : 'Desconocido',
            ],
            'vehiculo_concepto_id' => [
                'value' => $ordenservicio->vehiculo_concepto_id,
                'label' => optional($ordenservicio->vehiculo_concepto)->descripcion ?? 'Desconocido',
            ],
            'empresa' => [
                'value' => $ordenservicio->empresa_id,
                'label' => optional($ordenservicio->empresa)->nombre ?? 'Desconocido',
            ],
            'cliente' => [
                'value' => $ordenservicio->cliente_id,
                'label' => optional($ordenservicio->cliente)->nombre ?? 'Desconocido',
            ],
            'estimacion' => $ordenservicio->entrada->estimacion ?? null,
            'kilometraje' => $ordenservicio->entrada->kilometraje,
            'gasolina' => $ordenservicio->entrada->gasolina,
            'telefono' => $ordenservicio->telefono,
            'administrador' => optional($responsables->administrador_transporte)->nombre ?? null,
            'jefe' => optional($responsables->jefe_de_proceso)->nombre ?? null,
            'trabajador' => optional($responsables->trabajador)->nombre ?? null,
            'tecnico' => optional($responsables->tecnico)->nombre ?? null,
            'descripcion_mo' => $ordenservicio->fallas_reportadas,
            'indicaciones_cliente' => $recepcionVehicular?->indicaciones_cliente ?? '',
            'cambiar_archivos' => $recepcionVehicular?->cambiar_archivos ?? false,
        ];

        $pintura = [
            'decolorada' => (bool) $condicionespintura->decolorada,
            'color_desigual' => (bool) $condicionespintura->color_no_igual,
            'rayones' => (bool) $condicionespintura->exeso_rayones,
            'grietas' => (bool) $condicionespintura->pequenias_grietas,
            'golpes' => (bool) $condicionespintura->carroceria_golpes,
            'emblemas' => (bool) $condicionespintura->emblemas_completos,
            'logos' => (bool) $condicionespintura->logos,
            'rociado' => (bool) $condicionespintura->exeso_rociado,
            'granizo' => (bool) $condicionespintura->danios_granizado,
            'lluvia' => (bool) $condicionespintura->lluvia_acido,
        ];
        $inventario = [
            'llanta' => (bool) $inventariobase->llanta,
            'cables' => (bool) $inventariobase->cables_corriente,
            'estuche' => (bool) $inventariobase->estuche_herramientas,
            'llave_tuerca' => (bool) $inventariobase->llave_tuercas,
            'triangulo' => (bool) $inventariobase->triangulo_seguridad,
            'tarjeta_circulacion' => (bool) $inventariobase->tarjeta_circulacion,
            'cubreruedas' => (bool) $inventariobase->cubreruedas,
            'candado_rueda' => (bool) $inventariobase->candado_rueda,
            'extinguidor' => (bool) $inventariobase->extinguidor,
            'gato' => (bool) $inventariobase->gato,
            'placas' => (bool) $inventariobase->placas,
        ];
        $interiores = [
            'puerta_izq_f' => $interioresbase->puerta_interior_frontal,
            'puerta_izq_t' => $interioresbase->puerta_interior_trasera,
            'puerta_der_f' => $interioresbase->puerta_delantera_frontal,
            'puerta_der_t' => $interioresbase->puerta_delantera_trasera,
            'asiento_izq_f' => $interioresbase->asiento_interior_frontal,
            'asiento_izq_t' => $interioresbase->asiento_interior_trasera,
            'asiento_der_f' => $interioresbase->asiento_delantera_frontal,
            'asiento_der_t' => $interioresbase->asiento_delantera_trasera,
            'consola' => $interioresbase->consola_central,
            'claxon' => $interioresbase->claxon,
            'tablero' => $interioresbase->tablero,
            'quemacocos' => $interioresbase->quemacocos,
            'toldo' => $interioresbase->toldo,
            'elevadores' => $interioresbase->elevadores_eletricos,
            'luces' => $interioresbase->luces_interiores,
            'seguros' => $interioresbase->seguros_eletricos,
            'climatizador' => $interioresbase->climatizador,
            'radio' => $interioresbase->radio,
            'retrovisor' => $interioresbase->espejos_retrovizor,
            'tapetes' => $interioresbase->tapetes,
        ];
        $exteriores = [
            'antena_radio' => $exterioresbase->antena_radio,
            'estribos' => $exterioresbase->estribos,
            'antena_telefono' => $exterioresbase->antena_telefono,
            'guardafangos' => $exterioresbase->guardafangos,
            'antena_cb' => $exterioresbase->antena_cb,
            'parabrisas' => $exterioresbase->parabrisas,
            'alarma' => $exterioresbase->sistema_alarma,
            'limpiaparabrisas' => $exterioresbase->limpia_parabrisas,
            'luces' => $exterioresbase->luces_exteriores,
            'espejos_laterales' => $exterioresbase->espejos_laterales,
        ];
        $archivos = $recepcionVehicular?->archivos ?? collect([]);

        $pathcarro = RutasArchivo::where('tipo_id', 26)->where('estatus_id', 21)->first();
        $pathfirma = RutasArchivo::where('tipo_id', 25)->where('estatus_id', 21)->first();
        $pathevidencia = RutasArchivo::where('tipo_id', 58)->where('estatus_id', 21)->first();

        Log::info($archivos);
        $carro = $archivos->where('tipo_id', 26)->where('estatus_id', 21)->first();
        $firma = $archivos->where('tipo_id', 25)->where('estatus_id', 21)->first();
        $evidencia = $archivos->where('tipo_id', 58)->where('estatus_id', 21)->values();

        $urls = [];

        if ($pathcarro && $carro) {
            $urls['carro'] = ['id' => $carro->id, 'url' => Storage::disk($pathcarro->disk)->url($pathcarro->folder.'/'.$carro->nombre)];
        } else {
            $urls['carro'] = null;
        }

        if ($pathfirma && $firma) {
            $urls['firma'] = ['id' => $firma->id, 'url' => Storage::disk($pathfirma->disk)->url($pathfirma->folder.'/'.$firma->nombre)];
        } else {
            $urls['firma'] = null;
        }

        if ($pathevidencia && $evidencia->isNotEmpty()) {
            $urls['evidencia'] = $evidencia->map(function ($item) use ($pathevidencia) {
                return ['id' => $item->id, 'url' => Storage::disk($pathevidencia->disk)->url($pathevidencia->folder.'/'.$item->nombre),
                ];
            })->toArray();
        } else {
            $urls['evidencia'] = [];
        }

        return response()->json(compact('generales', 'pintura', 'interiores', 'exteriores', 'inventario', 'urls'));
    }
}
