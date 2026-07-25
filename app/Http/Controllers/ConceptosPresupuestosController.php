<?php

namespace App\Http\Controllers;

use App\Models\ConceptosPresupuestos;
use App\Models\CostosConceptosPresupuestos;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class ConceptosPresupuestosController extends Controller
{
    public function read(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'currentPage' => ['nullable', 'integer', 'min:1'],
            'itemsPerPage' => ['nullable', 'integer', 'min:1', 'max:200'],
            'search' => ['nullable', 'string', 'max:255'],
            'categoria_sat' => ['nullable', 'integer', 'exists:categorias_sat,id'],
            'unidad_sat' => ['nullable', 'integer', 'exists:unidades_sat,id'],
            'vehiculo' => ['nullable', 'integer', 'exists:vehiculos_conceptos,id'],
            'categoria' => [
                'nullable',
                'integer',
                Rule::exists('tipos', 'id')->where('categoria_id', 7),
            ],
            'modulos' => ['nullable', 'array'],
            'modulos.*' => ['integer', 'exists:modulo_ordenes_servicios,id'],
        ]);

        $currentPage = $validated['currentPage'] ?? 1;
        $itemsPerPage = $validated['itemsPerPage'] ?? 10;
        $search = trim($validated['search'] ?? '');

        $query = CostosConceptosPresupuestos::query()
            ->with([
                'concepto_presupuesto.modulo_orden_servicio',
                'concepto_presupuesto.categoria_sat',
                'concepto_presupuesto.unidad_sat',
                'concepto_presupuesto.tipo',
                'vehiculo_concepto',
                'usuario',
            ]);

        if ($search !== '') {
            $query->whereHas('concepto_presupuesto', function ($conceptoQuery) use ($search) {
                $conceptoQuery->where('descripcion', 'like', "%{$search}%");
            });
        }

        if (isset($validated['categoria_sat'])) {
            $query->whereHas('concepto_presupuesto', function ($conceptoQuery) use ($validated) {
                $conceptoQuery->where('categoria_sat_id', $validated['categoria_sat']);
            });
        }

        if (isset($validated['unidad_sat'])) {
            $query->whereHas('concepto_presupuesto', function ($conceptoQuery) use ($validated) {
                $conceptoQuery->where('unidad_sat_id', $validated['unidad_sat']);
            });
        }

        if (isset($validated['categoria'])) {
            $query->whereHas('concepto_presupuesto', function ($conceptoQuery) use ($validated) {
                $conceptoQuery->where('tipo_id', $validated['categoria']);
            });
        }

        if (isset($validated['vehiculo'])) {
            $query->where('vehiculo_concepto_id', $validated['vehiculo']);
        }

        if (($validated['modulos'] ?? []) !== []) {
            $query->whereHas('concepto_presupuesto', function ($conceptoQuery) use ($validated) {
                $conceptoQuery->whereIn('modulo_orden_servicio_id', $validated['modulos']);
            });
        }

        $paginator = $query
            ->orderByDesc('id')
            ->paginate($itemsPerPage, ['*'], 'page', $currentPage);

        $items = $paginator->getCollection()->map(function (CostosConceptosPresupuestos $costo) {
            $concepto = $costo->concepto_presupuesto;

            return [
                'id' => $costo->id,
                'concepto_id' => $costo->concepto_presupuesto_id,
                'descripcion' => $concepto?->descripcion ?? '',
                'modulo' => $concepto?->modulo_orden_servicio?->descripcion ?? '',
                'categoria_sat' => $concepto?->categoria_sat?->descripcion ?? '',
                'codigo_sat' => $concepto?->categoria_sat?->codigo_sat ?? '',
                'unidad_sat' => $concepto?->unidad_sat?->descripcion ?? '',
                'codigo_unidad_sat' => $concepto?->unidad_sat?->codigo ?? '',
                'tipo' => $concepto?->tipo?->descripcion ?? '',
                'vehiculo' => $costo->vehiculo_concepto?->descripcion ?? '',
                'usuario' => $costo->usuario?->name ?: ($costo->usuario?->usuario ?? ''),
                'total' => $costo->p_total,
            ];
        })->values();

        return response()->json([
            'currentPage' => $paginator->currentPage(),
            'itemsPerPage' => $paginator->perPage(),
            'totalPages' => $paginator->lastPage(),
            'totalItems' => $paginator->total(),
            'items' => $items,
        ]);
    }

    public function show(CostosConceptosPresupuestos $costo): JsonResponse
    {
        $costo->load([
            'concepto_presupuesto.tipo',
            'concepto_presupuesto.modulo_orden_servicio',
            'concepto_presupuesto.categoria_sat',
            'concepto_presupuesto.unidad_sat',
            'vehiculo_concepto',
        ]);
        $concepto = $costo->concepto_presupuesto;

        return response()->json([
            'id' => $costo->id,
            'numero' => $concepto?->num ?? '',
            'descripcion' => $concepto?->descripcion ?? '',
            'garantia_dias' => $concepto?->garantia_dias,
            'fijo' => (bool) ($concepto?->fijo ?? false),
            'tipo' => $concepto?->tipo ? [
                'value' => $concepto->tipo->id,
                'label' => $concepto->tipo->descripcion,
            ] : null,
            'modulo' => $concepto?->modulo_orden_servicio ? [
                'value' => $concepto->modulo_orden_servicio->id,
                'label' => $concepto->modulo_orden_servicio->descripcion,
            ] : null,
            'categoria_sat' => $concepto?->categoria_sat ? [
                'value' => $concepto->categoria_sat->id,
                'label' => "{$concepto->categoria_sat->descripcion} — {$concepto->categoria_sat->codigo_sat}",
            ] : null,
            'unidad_sat' => $concepto?->unidad_sat ? [
                'value' => $concepto->unidad_sat->id,
                'label' => "{$concepto->unidad_sat->descripcion} — {$concepto->unidad_sat->codigo}",
            ] : null,
            'vehiculo' => $costo->vehiculo_concepto ? [
                'value' => $costo->vehiculo_concepto->id,
                'label' => $costo->vehiculo_concepto->descripcion,
            ] : null,
            'p_refaccion' => $costo->p_refaccion,
            'p_mano_obra' => $costo->p_mano_obra,
            'p_total' => $costo->p_total,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $this->validateConcepto($request);

        $costo = DB::transaction(function () use ($validated) {
            $concepto = ConceptosPresupuestos::create([
                'num' => $validated['numero'],
                'descripcion' => $validated['descripcion'],
                'garantia_dias' => $validated['garantia_dias'] ?? null,
                'fijo' => false,
                'tipo_id' => $validated['tipo_id'],
                'modulo_orden_servicio_id' => $validated['modulo_id'],
                'categoria_sat_id' => $validated['categoria_sat_id'],
                'unidad_sat_id' => $validated['unidad_sat_id'],
            ]);

            return CostosConceptosPresupuestos::create([
                'concepto_presupuesto_id' => $concepto->id,
                'vehiculo_concepto_id' => $validated['vehiculo_id'],
                'usuario_id' => Auth::id(),
                'p_refaccion' => $validated['p_refaccion'],
                'p_mano_obra' => $validated['p_mano_obra'],
                'p_total' => $validated['p_refaccion'] + $validated['p_mano_obra'],
            ]);
        });

        return response()->json([
            'message' => 'Concepto creado correctamente.',
            'id' => $costo->id,
        ], 201);
    }

    public function update(Request $request, CostosConceptosPresupuestos $costo): JsonResponse
    {
        $validated = $this->validateConcepto($request);

        DB::transaction(function () use ($validated, $costo) {
            $costo->concepto_presupuesto()->update([
                'num' => $validated['numero'],
                'descripcion' => $validated['descripcion'],
                'garantia_dias' => $validated['garantia_dias'] ?? null,
                'tipo_id' => $validated['tipo_id'],
                'modulo_orden_servicio_id' => $validated['modulo_id'],
                'categoria_sat_id' => $validated['categoria_sat_id'],
                'unidad_sat_id' => $validated['unidad_sat_id'],
            ]);

            $costo->update([
                'vehiculo_concepto_id' => $validated['vehiculo_id'],
                'p_refaccion' => $validated['p_refaccion'],
                'p_mano_obra' => $validated['p_mano_obra'],
                'p_total' => $validated['p_refaccion'] + $validated['p_mano_obra'],
            ]);
        });

        return response()->json(['message' => 'Concepto actualizado correctamente.']);
    }

    public function destroy(CostosConceptosPresupuestos $costo): JsonResponse
    {
        DB::transaction(function () use ($costo) {
            $concepto = $costo->concepto_presupuesto;
            $costo->delete();

            if (
                $concepto
                && ! $concepto->costos()->exists()
                && ! $concepto->presupuestos_asignados()->exists()
            ) {
                $concepto->delete();
            }
        });

        return response()->json(['message' => 'Concepto eliminado correctamente.']);
    }

    private function validateConcepto(Request $request): array
    {
        return $request->validate(
            [
                'numero' => ['required', 'string', 'max:100'],
                'descripcion' => ['required', 'string', 'max:5000'],
                'garantia_dias' => ['nullable', 'integer', 'min:0', 'max:3650'],
                'tipo_id' => [
                    'required',
                    'integer',
                    Rule::exists('tipos', 'id')->where('categoria_id', 7),
                ],
                'modulo_id' => ['required', 'integer', 'exists:modulo_ordenes_servicios,id'],
                'categoria_sat_id' => ['required', 'integer', 'exists:categorias_sat,id'],
                'unidad_sat_id' => ['required', 'integer', 'exists:unidades_sat,id'],
                'vehiculo_id' => [
                    'required',
                    'integer',
                    Rule::exists('vehiculos_conceptos_disponibles', 'vehiculo_concepto_id')
                        ->where(
                            fn ($query) => $query
                                ->where('modulo_orden_id', $request->integer('modulo_id'))
                                ->whereNull('deleted_at')
                        ),
                ],
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
                'modulo_id' => 'módulo',
                'categoria_sat_id' => 'categoría SAT',
                'unidad_sat_id' => 'unidad SAT',
                'vehiculo_id' => 'vehículo',
                'p_refaccion' => 'precio de refacción',
                'p_mano_obra' => 'precio de mano de obra',
            ]
        );
    }
}
