<?php

namespace App\Services;

use App\Models\Estatus;
use App\Models\ValesAlmacen;
use Illuminate\Validation\ValidationException;

final class ValeAlmacenWorkflowService
{
    public const CATEGORIA = 13;

    public const TIPO_ALMACEN = 'Almacen';

    public const TIPO_SUBCONTRATO = 'Subcontrato';

    public const SIN_ENTREGAR = 'Sin Entregar';

    public const PEDIDO_ENTREGADO = 'Pedido Entregado';

    public const ENTREGADO = 'Entregado';

    public const CONFIRMADO = 'Confirmado';

    public const TRABAJO_ENVIADO = 'Trabajo Enviado';

    public const TRABAJO_AUTORIZADO = 'Trabajo Autorizado';

    /** @var array<string, list<string>> */
    private const FLUJOS = [
        self::TIPO_ALMACEN => [
            self::SIN_ENTREGAR,
            self::PEDIDO_ENTREGADO,
            self::ENTREGADO,
        ],
        self::TIPO_SUBCONTRATO => [
            self::SIN_ENTREGAR,
            self::TRABAJO_ENVIADO,
            self::TRABAJO_AUTORIZADO,
        ],
    ];

    public function siguiente(ValesAlmacen $vale): ?string
    {
        $vale->loadMissing(['tipoRegistro', 'estatusRegistro']);
        $flujo = self::FLUJOS[$vale->tipoRegistro?->descripcion] ?? [];
        $posicion = array_search($vale->estatusRegistro?->descripcion, $flujo, true);

        return $posicion === false ? null : ($flujo[$posicion + 1] ?? null);
    }

    public function avanzar(ValesAlmacen $vale): string
    {
        $siguiente = $this->siguiente($vale);

        if ($siguiente === null) {
            throw ValidationException::withMessages([
                'status' => 'El vale no tiene un siguiente estatus disponible.',
            ]);
        }

        $estatus = Estatus::query()
            ->where('categoria_id', self::CATEGORIA)
            ->where('descripcion', $siguiente)
            ->firstOrFail();

        $vale->update(['status' => $estatus->id]);
        $vale->setRelation('estatusRegistro', $estatus);

        return $siguiente;
    }

    public function sincronizarEntrega(ValesAlmacen $vale): string
    {
        $total = $vale->conceptos()->count();
        $entregados = $vale->conceptos()->whereNotNull('fecha_entrega')->count();
        $estatusActual = $vale->estatusRegistro?->descripcion ?? self::SIN_ENTREGAR;

        if ($total === 0 || $entregados === 0) {
            return $estatusActual;
        }

        $nuevoEstatus = $entregados === $total ? self::CONFIRMADO : self::ENTREGADO;
        $estatus = Estatus::query()
            ->where('categoria_id', self::CATEGORIA)
            ->where('descripcion', $nuevoEstatus)
            ->firstOrFail();

        $vale->update(['status' => $estatus->id]);
        $vale->setRelation('estatusRegistro', $estatus);

        return $nuevoEstatus;
    }

    public function alcanzo(string $tipo, string $estatusActual, string $estatus): bool
    {
        $flujo = self::FLUJOS[$tipo] ?? [];

        if ($tipo === self::TIPO_ALMACEN) {
            $flujo[] = self::CONFIRMADO;
        }

        $actual = array_search($estatusActual, $flujo, true);
        $objetivo = array_search($estatus, $flujo, true);

        return $actual !== false && $objetivo !== false && $actual >= $objetivo;
    }
}
