<?php

namespace App\Http\Controllers;

use App\Models\OrdenesServicio;
use App\Models\Subcontrato;
use App\Models\Taller;
use App\Models\User;
use App\Services\AlcanceOrdenesServicio;
use App\Services\AlcanceRecepcionesVehiculares;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class TallerSubcontratoController extends Controller
{
    public function talleres(Request $request): JsonResponse
    {
        $query = Taller::query()->orderBy('descripcion');

        if ($request->filled('query')) {
            $query->where(
                'descripcion',
                'like',
                '%'.$request->string('query')->trim().'%'
            );
        }

        $options = $query
            ->limit(50)
            ->get()
            ->map(fn (Taller $taller) => [
                'value' => $taller->id,
                'label' => $taller->descripcion,
                'externo' => $taller->externo,
            ]);

        return response()->json(compact('options'));
    }

    public function actualizarTaller(
        Request $request,
        OrdenesServicio $ordenServicio
    ): JsonResponse {
        $this->ensureVisible($request, $ordenServicio);

        $validated = $request->validate([
            'taller_id' => ['required', 'integer', 'exists:talleres,id'],
        ]);

        if ((int) $ordenServicio->taller_id === (int) $validated['taller_id']) {
            throw ValidationException::withMessages([
                'taller_id' => 'Selecciona un taller diferente al actual.',
            ]);
        }

        $ordenServicio->update([
            'taller_id' => $validated['taller_id'],
            'user_asignado' => null,
        ]);

        $taller = Taller::findOrFail($validated['taller_id']);

        return response()->json([
            'message' => 'Taller actualizado correctamente.',
            'taller' => [
                'value' => $taller->id,
                'label' => $taller->descripcion,
                'externo' => $taller->externo,
            ],
        ]);
    }

    public function usuariosAsignables(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'orden_servicio_id' => [
                'required',
                'integer',
                'exists:ordenes_servicio,id',
            ],
            'query' => ['nullable', 'string', 'max:255'],
        ]);
        $ordenServicio = OrdenesServicio::findOrFail(
            $validated['orden_servicio_id']
        );
        $this->ensureVisible($request, $ordenServicio);

        $query = User::query()
            ->where('taller_id', $ordenServicio->taller_id)
            ->orderBy('name');

        if ($request->filled('query')) {
            $search = $request->string('query')->trim();
            $query->where(function ($builder) use ($search) {
                $builder
                    ->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('usuario', 'like', "%{$search}%");
            });
        }

        $options = $query
            ->limit(50)
            ->get()
            ->map(fn (User $user) => [
                'value' => $user->id,
                'label' => str_replace('-', ' ', $user->name),
            ]);

        return response()->json(compact('options'));
    }

    public function asignarUsuario(
        Request $request,
        OrdenesServicio $ordenServicio
    ): JsonResponse {
        $this->ensureVisible($request, $ordenServicio);

        $validated = $request->validate([
            'user_id' => [
                'required',
                'integer',
                Rule::exists('users', 'id')
                    ->where('taller_id', $ordenServicio->taller_id)
                    ->whereNull('deleted_at'),
            ],
        ], [
            'user_id.exists' => 'El usuario debe estar activo y pertenecer al taller de la orden.',
        ]);

        if ((int) $ordenServicio->user_asignado === (int) $validated['user_id']) {
            throw ValidationException::withMessages([
                'user_id' => 'Selecciona un usuario diferente al actual.',
            ]);
        }

        $ordenServicio->update([
            'user_asignado' => $validated['user_id'],
        ]);

        $user = User::findOrFail($validated['user_id']);

        return response()->json([
            'message' => 'Usuario asignado correctamente.',
            'usuario' => [
                'value' => $user->id,
                'label' => str_replace('-', ' ', $user->name),
            ],
        ]);
    }

    public function historial(
        Request $request,
        OrdenesServicio $ordenServicio
    ): JsonResponse {
        $this->ensureVisible($request, $ordenServicio);

        $subcontratos = $ordenServicio
            ->subcontratos()
            ->with('usuario:id,name')
            ->orderByDesc('fecha_inicio')
            ->get()
            ->map(fn (Subcontrato $subcontrato) => [
                'id' => $subcontrato->id,
                'fecha_inicio' => $subcontrato->fecha_inicio?->toIso8601String(),
                'fecha_fin' => $subcontrato->fecha_fin?->toIso8601String(),
                'usuario' => str_replace(
                    '-',
                    ' ',
                    $subcontrato->usuario?->name ?? 'Usuario desconocido'
                ),
                'activo' => $subcontrato->fecha_fin === null,
            ]);

        return response()->json([
            'subcontratos' => $subcontratos,
            'tiene_activo' => $subcontratos->contains('activo', true),
        ]);
    }

    public function iniciar(
        Request $request,
        OrdenesServicio $ordenServicio
    ): JsonResponse {
        $this->ensureVisible($request, $ordenServicio);

        DB::transaction(function () use ($request, $ordenServicio) {
            $orden = OrdenesServicio::query()
                ->whereKey($ordenServicio->id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($orden->subcontratos()->whereNull('fecha_fin')->exists()) {
                throw ValidationException::withMessages([
                    'subcontrato' => 'La orden ya tiene un subcontrato activo.',
                ]);
            }

            $orden->subcontratos()->create([
                'fecha_inicio' => now(),
                'fecha_fin' => null,
                'user_id' => $request->user()->id,
            ]);
        });

        return response()->json([
            'message' => 'Subcontrato iniciado correctamente.',
        ]);
    }

    public function finalizar(
        Request $request,
        Subcontrato $subcontrato
    ): JsonResponse {
        $subcontrato->loadMissing('ordenServicio');
        $this->ensureVisible($request, $subcontrato->ordenServicio);

        $updated = Subcontrato::query()
            ->whereKey($subcontrato->id)
            ->whereNull('fecha_fin')
            ->update(['fecha_fin' => now()]);

        if ($updated === 0) {
            throw ValidationException::withMessages([
                'subcontrato' => 'El subcontrato ya fue finalizado.',
            ]);
        }

        return response()->json([
            'message' => 'Subcontrato finalizado correctamente.',
        ]);
    }

    private function ensureVisible(
        Request $request,
        OrdenesServicio $ordenServicio
    ): void {
        $puedeAcceder = AlcanceRecepcionesVehiculares::puedeAccederOrden(
            $request->user(),
            $ordenServicio
        ) || AlcanceOrdenesServicio::puedeAccederOrden(
            $request->user(),
            $ordenServicio
        );

        abort_unless(
            $puedeAcceder,
            403
        );
    }
}
