<?php

namespace App\Services;

use App\Models\OrdenesServicio;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

final class AlcanceRecepcionesVehiculares
{
    public const BASE = 'ver_recepciones_vehiculares';

    public const MODULOS = 'ver_ordenes_servicio_modulos';

    public const TODOS = 'ver_ordenes_servicio_todos';

    public static function nivel(User $user): ?string
    {
        if (! $user->can(self::BASE)) {
            return null;
        }

        if ($user->can(self::TODOS)) {
            return self::TODOS;
        }

        if ($user->can(self::MODULOS)) {
            return self::MODULOS;
        }

        return self::BASE;
    }

    public static function aplicar(
        Builder $query,
        User $user,
        string $ordenAlias = 'ordenes_servicio'
    ): Builder {
        return match (self::nivel($user)) {
            self::TODOS => $query,
            self::MODULOS => $query->whereIn(
                "{$ordenAlias}.modulo_orden_id",
                $user->modulos_orden()->select('modulo_orden_id')
            ),
            self::BASE => $query->where(
                "{$ordenAlias}.user_id",
                $user->id
            ),
            default => $query->whereRaw('1 = 0'),
        };
    }

    public static function puedeAccederOrden(
        User $user,
        OrdenesServicio $orden
    ): bool {
        return match (self::nivel($user)) {
            self::TODOS => true,
            self::MODULOS => $user->modulos_orden()
                ->where('modulo_orden_id', $orden->modulo_orden_id)
                ->exists(),
            self::BASE => (int) $orden->user_id === (int) $user->id,
            default => false,
        };
    }
}
