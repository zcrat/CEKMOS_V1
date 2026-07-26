<?php

namespace App\Http\Controllers;

use App\Models\CategoriasConceptosDisponibles;
use App\Models\CategoriasSat;
use App\Models\Clientes;
use App\Models\Empresas;
use App\Models\Marcas;
use App\Models\Modelos;
use App\Models\ModuloOrdenesServicio;
use App\Models\Motores;
use App\Models\OrdenesServicio;
use App\Models\Presupuestos;
use App\Models\RegimenesFiscales;
use App\Models\Tipos;
use App\Models\UnidadesSat;
use App\Models\Vehiculos;
use App\Models\VehiculosConceptos;
use App\Models\VehiculosConceptosDisponibles;
use App\Services\AlcanceOrdenesServicio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class select2controller extends Controller
{
    public function Empresas(Request $request)
    {
        $options = Empresas::query();
        if ($request->filled('query')) {
            $search = '%'.($request->input('query')).'%';
            $options = $options->where('nombre', 'LIKE', $search)->orWhere('rfc', 'LIKE', $search);
        }
        $options = $options->take(20)->get()->map(fn ($item) => [
            'value' => $item->id,
            'label' => $item->nombre.'-'.$item->rfc,
        ]);

        return response()->json(compact('options'));
    }

    public function Clientes(Request $request)
    {
        if ($request->filled('empresa_id')) {
            $options = Clientes::query()->where('empresa_id', $request->input('empresa_id'));
        } else {
            return response()->json(['options' => []]);
        }

        if ($request->filled('query')) {
            $search = '%'.($request->input('query')).'%';
            $options = $options->where('nombre', 'LIKE', $search)->orWhere('telefono', 'LIKE', $search);
        }
        $options = $options->take(20)->get()->map(fn ($item) => [
            'value' => $item->id,
            'label' => $item->nombre.'-'.$item->telefono,
        ]);

        return response()->json(compact('options'));
    }

    public function Economicos(Request $request)
    {
        $options = Vehiculos::query();
        if ($request->filled('query')) {
            $search = '%'.($request->input('query')).'%';
            $options = $options->where('economico', 'LIKE', $search)->orWhere('placas', 'LIKE', $search);
        }
        $options = $options->take(20)->get()->map(fn ($item) => [
            'value' => $item->id,
            'label' => $item->economico.'-'.$item->placas,
        ]);

        return response()->json(compact('options'));
    }

    public function RegimenesFiscales(Request $request)
    {
        $options = RegimenesFiscales::query();
        if ($request->filled('query')) {
            $search = '%'.($request->input('query')).'%';
            $options = $options->where('descripcion', 'LIKE', $search)->orWhere('clave', 'LIKE', $search);
        }
        $options = $options->take(20)->get()->map(fn ($item) => [
            'value' => $item->clave,
            'label' => $item->clave.'-'.$item->descripcion,
        ]);

        return response()->json(compact('options'));
    }

    public function VehiculosConceptosPorModulo(Request $request)
    {

        $validation = Validator::make($request->all(), ['id_modulo' => ['required', 'exists:modulo_ordenes_servicios,id'], 'query' => ['nullable', 'string']]);
        $options = [];
        if (! $validation->fails()) {
            $search = '%'.$validation->validated()['query'].'%';
            $options = VehiculosConceptos::query()->join('vehiculos_conceptos_disponibles', 'vehiculos_conceptos_disponibles.vehiculo_concepto_id', '=', 'vehiculos_conceptos.id')
                ->where('vehiculos_conceptos_disponibles.modulo_orden_id', $validation->validated()['id_modulo'])
                ->where('descripcion', 'LIKE', $search)
                ->get()->map(fn ($item) => [
                    'value' => $item->id,
                    'label' => $item->descripcion,
                ]);
        }

        return response()->json(compact('options'));
    }

    public function MarcasCatalogo(Request $request)
    {
        $search = '%'.trim($request->input('query', '')).'%';
        $options = Marcas::query()
            ->where('descripcion', 'LIKE', $search)
            ->orderBy('descripcion')
            ->take(20)
            ->get()
            ->map(fn ($item) => [
                'value' => $item->id,
                'label' => $item->descripcion,
            ]);

        return response()->json(compact('options'));
    }

    public function MotoresCatalogo(Request $request)
    {
        $search = '%'.trim($request->input('query', '')).'%';
        $options = Motores::query()
            ->where('descripcion', 'LIKE', $search)
            ->orderBy('descripcion')
            ->take(20)
            ->get()
            ->map(fn ($item) => [
                'value' => $item->id,
                'label' => $item->descripcion,
            ]);

        return response()->json(compact('options'));
    }

    public function ModelosCatalogo(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'marca_id' => ['required', 'exists:marcas,id'],
            'query' => ['nullable', 'string'],
        ]);
        if ($validation->fails()) {
            return response()->json(['options' => []]);
        }

        $validated = $validation->validated();
        $search = '%'.trim($validated['query'] ?? '').'%';
        $options = Modelos::query()
            ->where('marca_id', $validated['marca_id'])
            ->where('descripcion', 'LIKE', $search)
            ->select('descripcion')
            ->distinct()
            ->orderBy('descripcion')
            ->take(20)
            ->get()
            ->map(fn ($item) => [
                'value' => $item->descripcion,
                'label' => $item->descripcion,
            ]);

        return response()->json(compact('options'));
    }

    public function CategoriasSatCatalogo(Request $request)
    {
        $query = trim((string) $request->input('query', ''));
        $options = CategoriasSat::query()
            ->when($query !== '', function ($builder) use ($query) {
                $builder->where(function ($searchQuery) use ($query) {
                    $searchQuery
                        ->where('descripcion', 'like', "%{$query}%")
                        ->orWhere('codigo_sat', 'like', "%{$query}%");
                });
            })
            ->orderBy('descripcion')
            ->limit(50)
            ->get()
            ->map(fn (CategoriasSat $item) => [
                'value' => $item->id,
                'label' => "{$item->descripcion} — {$item->codigo_sat}",
            ]);

        return response()->json(compact('options'));
    }

    public function UnidadesSatCatalogo(Request $request)
    {
        $query = trim((string) $request->input('query', ''));
        $options = UnidadesSat::query()
            ->when($query !== '', function ($builder) use ($query) {
                $builder->where(function ($searchQuery) use ($query) {
                    $searchQuery
                        ->where('descripcion', 'like', "%{$query}%")
                        ->orWhere('codigo', 'like', "%{$query}%");
                });
            })
            ->orderBy('descripcion')
            ->limit(50)
            ->get()
            ->map(fn (UnidadesSat $item) => [
                'value' => $item->id,
                'label' => "{$item->descripcion} — {$item->codigo}",
            ]);

        return response()->json(compact('options'));
    }

    public function VehiculosConceptosCatalogo(Request $request)
    {
        $validated = $request->validate([
            'query' => ['nullable', 'string', 'max:255'],
            'modulo_id' => ['nullable', 'integer', 'exists:modulo_ordenes_servicios,id'],
        ]);
        $query = trim($validated['query'] ?? '');

        $options = VehiculosConceptosDisponibles::query()
            ->select('vehiculo_concepto_id')
            ->distinct()
            ->with('vehiculo_concepto:id,descripcion')
            ->when(
                isset($validated['modulo_id']),
                fn ($builder) => $builder->where('modulo_orden_id', $validated['modulo_id'])
            )
            ->when($query !== '', function ($builder) use ($query) {
                $builder->whereHas(
                    'vehiculo_concepto',
                    fn ($vehicleQuery) => $vehicleQuery->where('descripcion', 'like', "%{$query}%")
                );
            })
            ->orderBy('vehiculo_concepto_id')
            ->limit(50)
            ->get()
            ->map(fn (VehiculosConceptosDisponibles $item) => [
                'value' => $item->vehiculo_concepto_id,
                'label' => $item->vehiculo_concepto?->descripcion ?? '',
            ])
            ->filter(fn (array $item) => $item['label'] !== '')
            ->values();

        return response()->json(compact('options'));
    }

    public function ModulosCambioOrdenServicio(Request $request)
    {
        $validated = $request->validate([
            'orden_servicio_id' => [
                'required',
                'integer',
                'exists:ordenes_servicio,id',
            ],
            'query' => ['nullable', 'string', 'max:255'],
        ]);
        $order = OrdenesServicio::findOrFail($validated['orden_servicio_id']);
        $user = $request->user();

        abort_unless(
            AlcanceOrdenesServicio::puedeAccederOrden($user, $order),
            403
        );

        $visibleModules = $user->can(AlcanceOrdenesServicio::TODOS)
            ? null
            : $user->modulos_orden()->pluck('modulo_orden_id');
        $query = trim($validated['query'] ?? '');
        $options = ModuloOrdenesServicio::query()
            ->when(
                $visibleModules !== null,
                fn ($builder) => $builder->whereIn('id', $visibleModules)
            )
            ->where('id', '!=', $order->modulo_orden_id)
            ->whereHas(
                'vehiculos_conceptos',
                fn ($builder) => $builder->where(
                    'vehiculo_concepto_id',
                    $order->vehiculo_concepto_id
                )
            )
            ->when($query !== '', function ($builder) use ($query) {
                $builder->where(function ($search) use ($query) {
                    $search
                        ->where('descripcion', 'like', "%{$query}%")
                        ->orWhere('clave', 'like', "%{$query}%");
                });
            })
            ->orderByDesc('año')
            ->orderBy('descripcion')
            ->limit(50)
            ->get()
            ->map(fn (ModuloOrdenesServicio $module) => [
                'value' => $module->id,
                'label' => "{$module->descripcion} — {$module->año}",
            ]);

        return response()->json(compact('options'));
    }

    public function CategoriasConceptosCatalogo(Request $request)
    {
        $query = trim((string) $request->input('query', ''));
        $options = Tipos::query()
            ->where('categoria_id', 7)
            ->when(
                $query !== '',
                fn ($builder) => $builder->where('descripcion', 'like', "%{$query}%")
            )
            ->orderBy('descripcion')
            ->limit(50)
            ->get()
            ->map(fn (Tipos $item) => [
                'value' => $item->id,
                'label' => $item->descripcion,
            ]);

        return response()->json(compact('options'));
    }

    public function CategoriasConceptosPorPresupuesto(Request $request)
    {
        $validated = $request->validate([
            'presupuesto_id' => ['required', 'integer', 'exists:presupuestos,id'],
            'query' => ['nullable', 'string', 'max:255'],
        ]);
        $presupuesto = Presupuestos::query()
            ->with('orden_servicio')
            ->findOrFail($validated['presupuesto_id']);
        $user = $request->user();

        abort_unless(
            AlcanceOrdenesServicio::puedeAcceder($user, $presupuesto),
            403
        );

        $query = trim($validated['query'] ?? '');
        $options = CategoriasConceptosDisponibles::query()
            ->where('tipo_presupuesto_id', $presupuesto->tipo_id)
            ->whereHas('categoria_concepto', function ($builder) use ($query) {
                $builder
                    ->where('categoria_id', 7)
                    ->when(
                        $query !== '',
                        fn ($categoryQuery) => $categoryQuery
                            ->where('descripcion', 'like', "%{$query}%")
                    );
            })
            ->with('categoria_concepto:id,descripcion')
            ->get()
            ->sortBy(fn (CategoriasConceptosDisponibles $item) => (
                $item->categoria_concepto?->descripcion ?? ''
            ))
            ->map(fn (CategoriasConceptosDisponibles $item) => [
                'value' => $item->categoria_concepto_id,
                'label' => $item->categoria_concepto?->descripcion ?? '',
            ])
            ->filter(fn (array $item) => $item['label'] !== '')
            ->values();

        return response()->json(compact('options'));
    }

    public function ModulosOrdenCatalogo(Request $request)
    {
        $query = trim((string) $request->input('query', ''));
        $options = ModuloOrdenesServicio::query()
            ->when($query !== '', function ($builder) use ($query) {
                $builder->where(function ($searchQuery) use ($query) {
                    $searchQuery
                        ->where('descripcion', 'like', "%{$query}%")
                        ->orWhere('clave', 'like', "%{$query}%");
                });
            })
            ->orderBy('descripcion')
            ->limit(50)
            ->get()
            ->map(fn (ModuloOrdenesServicio $item) => [
                'value' => $item->id,
                'label' => $item->descripcion,
            ]);

        return response()->json(compact('options'));
    }
}
