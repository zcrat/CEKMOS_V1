<?php

namespace App\Http\Controllers;

use App\Models\ModuloOrdenesServicio;
use App\Models\OrdenesServicio;
use App\Models\VehiculosConceptosDisponibles;
use App\Services\AlcanceOrdenesServicio;
use App\Services\AlcanceRecepcionesVehiculares;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class OrdenesServicioController extends Controller
{
    public function actualizarModulo(
        Request $request,
        OrdenesServicio $ordenServicio
    ): JsonResponse {
        $this->ensureVisible($request, $ordenServicio);
        $validated = $request->validate([
            'modulo_id' => [
                'required',
                'integer',
                Rule::exists('modulo_ordenes_servicios', 'id')
                    ->whereNull('deleted_at'),
            ],
        ]);
        $moduleId = $validated['modulo_id'];

        if ((int) $ordenServicio->modulo_orden_id === (int) $moduleId) {
            throw ValidationException::withMessages([
                'modulo_id' => 'Selecciona un módulo diferente al actual.',
            ]);
        }

        if (! $request->user()->can(AlcanceOrdenesServicio::TODOS)) {
            $hasAccess = $request->user()
                ->modulos_orden()
                ->where('modulo_orden_id', $moduleId)
                ->exists();

            abort_unless($hasAccess, 403);
        }

        $vehicleIsAvailable = VehiculosConceptosDisponibles::query()
            ->where('modulo_orden_id', $moduleId)
            ->where(
                'vehiculo_concepto_id',
                $ordenServicio->vehiculo_concepto_id
            )
            ->exists();

        if (! $vehicleIsAvailable) {
            throw ValidationException::withMessages([
                'modulo_id' => 'El vehículo de la orden no está disponible en el módulo seleccionado.',
            ]);
        }

        $ordenServicio->update([
            'modulo_orden_id' => $moduleId,
        ]);
        $module = ModuloOrdenesServicio::findOrFail($moduleId);

        return response()->json([
            'message' => 'Módulo de la orden y sus presupuestos actualizado correctamente.',
            'modulo' => [
                'value' => $module->id,
                'label' => $module->descripcion,
            ],
        ]);
    }

    private function ensureVisible(
        Request $request,
        OrdenesServicio $ordenServicio
    ): void {
        abort_unless(
            AlcanceOrdenesServicio::puedeAccederOrden(
                $request->user(),
                $ordenServicio
            ) || AlcanceRecepcionesVehiculares::puedeAccederOrden(
                $request->user(),
                $ordenServicio
            ),
            403
        );
    }
}
