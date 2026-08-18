<?php

namespace App\Http\Controllers;

use App\Models\ConceptosPerValeAlmacen;
use App\Models\ValesAlmacen;
use App\Services\AlcanceRecepcionesVehiculares;
use App\Services\ValeAlmacenWorkflowService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class ValesSeguimientoController extends Controller
{
    private const TIPOS = [
        'almacen' => ValeAlmacenWorkflowService::TIPO_ALMACEN,
        'subcontratos' => ValeAlmacenWorkflowService::TIPO_SUBCONTRATO,
    ];

    private const PERMISOS = [
        'almacen' => 'ver.vales.almacen',
        'subcontratos' => 'ver.vales.subcontratos',
    ];

    public function __construct(private readonly ValeAlmacenWorkflowService $workflow) {}

    public function view(Request $request, string $tipo): Response
    {
        $this->authorizeTipo($request, $tipo);

        return Inertia::render('Cortana/ValesSeguimiento', [
            'tipo' => $tipo,
        ]);
    }

    public function index(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'tipo' => ['required', Rule::in(array_keys(self::TIPOS))],
            'currentPage' => ['nullable', 'integer', 'min:1'],
            'itemsPerPage' => ['nullable', 'integer', 'min:1', 'max:200'],
            'search' => ['nullable', 'string', 'max:255'],
        ]);
        $this->authorizeTipo($request, $validated['tipo']);
        $tipoDescripcion = self::TIPOS[$validated['tipo']];
        $currentPage = (int) ($validated['currentPage'] ?? 1);
        $itemsPerPage = (int) ($validated['itemsPerPage'] ?? 10);
        $search = trim($validated['search'] ?? '');

        $query = ValesAlmacen::query()
            ->select('vales_almacen.*')
            ->join('tipos as tipo_vale', 'tipo_vale.id', '=', 'vales_almacen.tipo')
            ->join('ordenes_servicio as orden', 'orden.id', '=', 'vales_almacen.orden_servicio_id')
            ->where('tipo_vale.descripcion', $tipoDescripcion)
            ->whereNull('orden.deleted_at')
            ->with(['orden_servicio', 'estatusRegistro'])
            ->withCount('conceptos')
            ->withCount([
                'conceptos as conceptos_entregados_count' => fn ($conceptos) => $conceptos->whereNotNull('fecha_entrega'),
            ])
            ->withMax('conceptos', 'fecha_entrega');

        AlcanceRecepcionesVehiculares::aplicar($query, $request->user(), 'orden');

        if ($search !== '') {
            $query->where(function ($filter) use ($search) {
                $filter
                    ->where('orden.orden_servicio', 'like', "%{$search}%")
                    ->orWhere('vales_almacen.destino', 'like', "%{$search}%");

                if (ctype_digit($search)) {
                    $filter->orWhere('vales_almacen.id', (int) $search);
                }
            });
        }

        $paginator = $query
            ->latest('vales_almacen.id')
            ->paginate($itemsPerPage, ['*'], 'page', $currentPage);

        $items = $paginator->getCollection()
            ->map(function (ValesAlmacen $vale) use ($tipoDescripcion) {
                $estatus = $vale->estatusRegistro?->descripcion ?? '';
                $base = [
                    'id' => $vale->id,
                    'orden' => $vale->orden_servicio?->orden_servicio ?? '',
                    'folio' => str_pad((string) $vale->id, 5, '0', STR_PAD_LEFT).'-01',
                    'creacion' => $vale->created_at?->format('d/m/Y H:i'),
                    'estatus' => $estatus,
                    'destino' => $vale->destino,
                ];

                if ($tipoDescripcion === ValeAlmacenWorkflowService::TIPO_ALMACEN) {
                    return $base + [
                        'entregado' => $this->workflow->alcanzo($tipoDescripcion, $estatus, ValeAlmacenWorkflowService::ENTREGADO),
                        'confirmado' => $estatus === ValeAlmacenWorkflowService::CONFIRMADO,
                        'fecha_termino' => $vale->conceptos_max_fecha_entrega
                            ? date('d/m/Y H:i', strtotime($vale->conceptos_max_fecha_entrega))
                            : null,
                        'refacciones_entregadas' => (int) $vale->conceptos_entregados_count,
                        'refacciones_total' => (int) $vale->conceptos_count,
                    ];
                }

                return $base + [
                    'enviado' => $this->workflow->alcanzo($tipoDescripcion, $estatus, ValeAlmacenWorkflowService::TRABAJO_ENVIADO),
                    'autorizado' => $this->workflow->alcanzo($tipoDescripcion, $estatus, ValeAlmacenWorkflowService::TRABAJO_AUTORIZADO),
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

    public function advance(Request $request, ValesAlmacen $vale): JsonResponse
    {
        $this->authorizeVale($request, $vale);

        $estatus = DB::transaction(fn () => $this->workflow->avanzar($vale));
        $vale->load(['tipoRegistro', 'estatusRegistro']);

        return response()->json([
            'message' => "El vale avanzó al estatus {$estatus}.",
            'vale' => $this->summary($vale),
        ]);
    }

    public function deliveries(Request $request, ValesAlmacen $vale): JsonResponse
    {
        $this->authorizeVale($request, $vale);
        $this->assertWarehouse($vale);
        $vale->load(['conceptos.concepto', 'estatusRegistro']);

        return response()->json([
            'vale' => [
                'id' => $vale->id,
                'folio' => str_pad((string) $vale->id, 5, '0', STR_PAD_LEFT).'-01',
                'orden' => $vale->orden_servicio?->orden_servicio ?? '',
                'estatus' => $vale->estatusRegistro?->descripcion ?? '',
            ],
            'conceptos' => $vale->conceptos->map(fn (ConceptosPerValeAlmacen $detalle) => [
                'id' => $detalle->id,
                'cantidad' => (float) $detalle->cantidad,
                'clave' => $detalle->concepto?->clave ?? '',
                'concepto' => $detalle->concepto?->descripcion ?? '',
                'fecha_entrega' => $detalle->fecha_entrega?->format('Y-m-d\TH:i'),
            ])->values(),
        ]);
    }

    public function storeDeliveries(Request $request, ValesAlmacen $vale): JsonResponse
    {
        $this->authorizeVale($request, $vale);
        $this->assertWarehouse($vale);
        $vale->loadMissing('estatusRegistro');

        abort_unless(
            in_array($vale->estatusRegistro?->descripcion, [
                ValeAlmacenWorkflowService::PEDIDO_ENTREGADO,
                ValeAlmacenWorkflowService::ENTREGADO,
                ValeAlmacenWorkflowService::CONFIRMADO,
            ], true),
            409,
            'El pedido debe marcarse como Pedido Entregado antes de registrar entregas.'
        );

        $validated = $request->validate([
            'entregas' => ['required', 'array', 'min:1'],
            'entregas.*.id' => [
                'required',
                'integer',
                'distinct',
                Rule::exists('conceptos_per_vale_almacen', 'id')
                    ->where('vale_almacen_id', $vale->id),
            ],
            'entregas.*.fecha_entrega' => ['required', 'date', 'before_or_equal:now'],
        ], [
            'entregas.required' => 'Selecciona al menos un concepto para entregar.',
            'entregas.min' => 'Selecciona al menos un concepto para entregar.',
            'entregas.*.id.exists' => 'Uno de los conceptos no pertenece al vale.',
            'entregas.*.fecha_entrega.required' => 'Captura la fecha de entrega de cada concepto seleccionado.',
            'entregas.*.fecha_entrega.before_or_equal' => 'La fecha de entrega no puede ser futura.',
        ]);

        $estatus = DB::transaction(function () use ($validated, $vale) {
            foreach ($validated['entregas'] as $entrega) {
                $vale->conceptos()
                    ->whereKey($entrega['id'])
                    ->update(['fecha_entrega' => $entrega['fecha_entrega']]);
            }

            return $this->workflow->sincronizarEntrega($vale);
        });

        return response()->json([
            'message' => "Entregas registradas. Estatus actual: {$estatus}.",
            'estatus' => $estatus,
        ]);
    }

    private function summary(ValesAlmacen $vale): array
    {
        return [
            'id' => $vale->id,
            'status' => (int) $vale->status,
            'estatus' => $vale->estatusRegistro?->descripcion ?? '',
            'tipo' => (int) $vale->tipo,
            'tipo_descripcion' => $vale->tipoRegistro?->descripcion ?? '',
            'siguiente_estatus' => $this->workflow->siguiente($vale),
        ];
    }

    private function assertWarehouse(ValesAlmacen $vale): void
    {
        $vale->loadMissing('tipoRegistro');

        abort_unless(
            $vale->tipoRegistro?->descripcion === ValeAlmacenWorkflowService::TIPO_ALMACEN,
            422,
            'Las entregas de conceptos sólo aplican a vales de almacén.'
        );
    }

    private function authorizeVale(Request $request, ValesAlmacen $vale): void
    {
        $vale->loadMissing('orden_servicio');

        abort_unless(
            $vale->orden_servicio
            && AlcanceRecepcionesVehiculares::puedeAccederOrden($request->user(), $vale->orden_servicio),
            403
        );
    }

    private function authorizeTipo(Request $request, string $tipo): void
    {
        abort_unless(isset(self::TIPOS[$tipo]), 404);
        abort_unless($request->user()?->can(self::PERMISOS[$tipo]), 403);
    }
}
