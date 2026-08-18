<?php

namespace App\Http\Controllers;

use App\Models\ConceptosAlmacen;
use App\Models\Estatus;
use App\Models\OrdenesServicio;
use App\Models\Tipos;
use App\Models\ValesAlmacen;
use App\Services\AlcanceRecepcionesVehiculares;
use App\Services\ValeAlmacenPdfService;
use App\Services\ValeAlmacenWorkflowService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ValesAlmacenController extends Controller
{
    private const CATEGORIA_VALES_ALMACEN = 13;

    private const ESTATUS_SIN_ENTREGAR = 'Sin Entregar';

    public function __construct(
        private readonly ValeAlmacenPdfService $valeAlmacenPdf,
        private readonly ValeAlmacenWorkflowService $workflow
    ) {}

    public function index(Request $request, OrdenesServicio $ordenServicio): JsonResponse
    {
        $this->authorizeOrder($request, $ordenServicio);

        $ordenServicio->load([
            'vehiculo.modelo.motor',
            'vales_almacen' => fn ($query) => $query
                ->select(['id', 'orden_servicio_id', 'tipo', 'status'])
                ->with(['tipoRegistro:id,descripcion', 'estatusRegistro:id,descripcion'])
                ->latest('id'),
        ]);

        return response()->json([
            'orden' => [
                'id' => $ordenServicio->id,
                'numero' => $ordenServicio->orden_servicio,
                'economico' => $ordenServicio->vehiculo?->economico ?? '',
                'placas' => $ordenServicio->vehiculo?->placas ?? '',
                'motor' => $ordenServicio->vehiculo?->modelo?->motor?->descripcion ?? '',
            ],
            'vales' => $ordenServicio->vales_almacen
                ->map(fn (ValesAlmacen $vale) => $this->serializeValeSummary($vale))
                ->values(),
        ]);
    }

    public function show(Request $request, ValesAlmacen $vale): JsonResponse
    {
        $this->authorizeVale($request, $vale);
        $vale->loadMissing('estatusRegistro');

        abort_unless(
            $vale->estatusRegistro?->descripcion === self::ESTATUS_SIN_ENTREGAR,
            409,
            'Sólo se pueden editar vales con estatus Sin Entregar.'
        );

        $vale->load('conceptos.concepto');

        return response()->json([
            'vale' => $this->serializeVale($vale),
        ]);
    }

    public function store(Request $request, OrdenesServicio $ordenServicio): JsonResponse
    {
        $this->authorizeOrder($request, $ordenServicio);
        $validated = $this->validateVale($request);

        $vale = DB::transaction(function () use ($validated, $ordenServicio, $request) {
            $tipo = Tipos::query()
                ->where('categoria_id', self::CATEGORIA_VALES_ALMACEN)
                ->where('descripcion', 'Almacen')
                ->firstOrFail();
            $estatus = Estatus::query()
                ->where('categoria_id', self::CATEGORIA_VALES_ALMACEN)
                ->where('descripcion', self::ESTATUS_SIN_ENTREGAR)
                ->firstOrFail();

            $vale = ValesAlmacen::create([
                'orden_servicio_id' => $ordenServicio->id,
                'destino' => mb_strtoupper(trim($validated['destino'])),
                'motor' => mb_strtoupper(trim($validated['motor'])),
                'user_id' => $request->user()->id,
                'tipo' => $tipo->id,
                'status' => $estatus->id,
            ]);

            $this->syncConceptos($vale, $validated['conceptos']);

            return $vale;
        });

        $this->valeAlmacenPdf->generate($vale);
        $vale->load(['tipoRegistro', 'estatusRegistro', 'conceptos.concepto']);

        return response()->json([
            'message' => 'Vale de almacén creado correctamente.',
            'vale' => $this->serializeVale($vale),
        ], 201);
    }

    public function update(Request $request, ValesAlmacen $vale): JsonResponse
    {
        $this->authorizeVale($request, $vale);
        $vale->loadMissing('estatusRegistro');

        abort_unless(
            $vale->estatusRegistro?->descripcion === self::ESTATUS_SIN_ENTREGAR,
            409,
            'Sólo se pueden editar vales con estatus Sin Entregar.'
        );

        $validated = $this->validateVale($request);

        DB::transaction(function () use ($validated, $vale) {
            $vale->update([
                'destino' => mb_strtoupper(trim($validated['destino'])),
                'motor' => mb_strtoupper(trim($validated['motor'])),
            ]);
            $vale->conceptos()->delete();
            $this->syncConceptos($vale, $validated['conceptos']);
        });

        $this->valeAlmacenPdf->generate($vale);
        $vale->load(['tipoRegistro', 'estatusRegistro', 'conceptos.concepto']);

        return response()->json([
            'message' => 'Vale de almacén actualizado correctamente.',
            'vale' => $this->serializeVale($vale),
        ]);
    }

    public function destroy(Request $request, ValesAlmacen $vale): JsonResponse
    {
        $this->authorizeVale($request, $vale);

        DB::transaction(function () use ($vale) {
            $vale->conceptos()->delete();
            $vale->delete();
        });
        $this->valeAlmacenPdf->delete($vale);

        return response()->json([
            'message' => 'Vale de almacén eliminado correctamente.',
        ]);
    }

    private function validateVale(Request $request): array
    {
        return $request->validate([
            'destino' => ['required', 'string', 'max:100'],
            'motor' => ['required', 'string', 'max:100'],
            'conceptos' => ['required', 'array', 'min:1'],
            'conceptos.*.clave' => ['nullable', 'string', 'max:50'],
            'conceptos.*.descripcion' => ['required', 'string', 'max:1000'],
            'conceptos.*.cantidad' => ['required', 'numeric', 'gt:0', 'max:999999.99'],
        ], [
            'destino.required' => 'El destino es obligatorio.',
            'destino.string' => 'El destino debe ser texto.',
            'destino.max' => 'El destino no puede tener más de 100 caracteres.',
            'motor.required' => 'El motor es obligatorio.',
            'motor.string' => 'El motor debe ser texto.',
            'motor.max' => 'El motor no puede tener más de 100 caracteres.',
            'conceptos.required' => 'Debe agregar al menos un concepto.',
            'conceptos.array' => 'Los conceptos deben enviarse como una lista.',
            'conceptos.min' => 'Debe agregar al menos un concepto.',
            'conceptos.*.clave.string' => 'La clave de cada concepto debe ser texto.',
            'conceptos.*.clave.max' => 'La clave de cada concepto no puede tener más de 50 caracteres.',
            'conceptos.*.descripcion.required' => 'La descripción de cada concepto es obligatoria.',
            'conceptos.*.descripcion.string' => 'La descripción de cada concepto debe ser texto.',
            'conceptos.*.descripcion.max' => 'La descripción de cada concepto no puede tener más de 1000 caracteres.',
            'conceptos.*.cantidad.required' => 'La cantidad de cada concepto es obligatoria.',
            'conceptos.*.cantidad.numeric' => 'La cantidad de cada concepto debe ser numérica.',
            'conceptos.*.cantidad.gt' => 'La cantidad de cada concepto debe ser mayor que cero.',
            'conceptos.*.cantidad.max' => 'La cantidad de cada concepto no puede ser mayor que 999999.99.',
        ]);
    }

    private function syncConceptos(ValesAlmacen $vale, array $conceptos): void
    {
        foreach ($conceptos as $item) {
            $clave = filled($item['clave'] ?? null)
                ? mb_strtoupper(trim($item['clave']))
                : null;
            $descripcion = mb_strtoupper(trim($item['descripcion']));

            $concepto = $clave
                ? ConceptosAlmacen::firstOrCreate(['clave' => $clave], ['descripcion' => $descripcion])
                : ConceptosAlmacen::firstOrCreate(['clave' => null, 'descripcion' => $descripcion]);

            $vale->conceptos()->create([
                'concepto_id' => $concepto->id,
                'cantidad' => $item['cantidad'],
            ]);
        }
    }

    private function serializeVale(ValesAlmacen $vale): array
    {
        return [
            'id' => $vale->id,
            'status' => (int) $vale->status,
            'tipo' => (int) $vale->tipo,
            'tipo_descripcion' => $vale->tipoRegistro?->descripcion ?? '',
            'siguiente_estatus' => $this->workflow->siguiente($vale),
            'folio' => str_pad((string) $vale->id, 5, '0', STR_PAD_LEFT).'-01',
            'destino' => $vale->destino,
            'motor' => $vale->motor,
            'fecha' => $vale->created_at?->format('d/m/Y H:i'),
            'estatus' => $vale->estatusRegistro?->descripcion ?? '',
            'pdf_url' => route('pdf.cortana.vale-almacen', $vale->id),
            'conceptos' => $vale->conceptos->map(fn ($detalle) => [
                'clave' => $detalle->concepto?->clave ?? '',
                'cantidad' => (float) $detalle->cantidad,
                'descripcion' => $detalle->concepto?->descripcion ?? '',
            ])->values(),
        ];
    }

    private function serializeValeSummary(ValesAlmacen $vale): array
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

    private function authorizeVale(Request $request, ValesAlmacen $vale): void
    {
        $vale->loadMissing('orden_servicio');
        $this->authorizeOrder($request, $vale->orden_servicio);
    }

    private function authorizeOrder(Request $request, OrdenesServicio $ordenServicio): void
    {
        abort_unless(
            AlcanceRecepcionesVehiculares::puedeAccederOrden($request->user(), $ordenServicio),
            403
        );
    }
}
