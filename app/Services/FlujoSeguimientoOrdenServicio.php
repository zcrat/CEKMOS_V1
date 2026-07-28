<?php

namespace App\Services;

final class FlujoSeguimientoOrdenServicio
{
    /**
     * @var array<string, array<string, array{descripcion: string, destino: string, permiso: string}>>
     */
    private const TRANSICIONES = [
        '' => [
            'iniciar_diagnostico' => [
                'descripcion' => 'Iniciar diagnóstico',
                'destino' => 'Diagnostico Iniciado',
                'permiso' => 'iniciar_terminar_diagnostico',
            ],
        ],
        'Diagnostico Iniciado' => [
            'terminar_diagnostico' => [
                'descripcion' => 'Terminar diagnóstico',
                'destino' => 'Diagnostico Terminado',
                'permiso' => 'iniciar_terminar_diagnostico',
            ],
        ],
        'Diagnostico Terminado' => [
            'terminar_mano_obra' => [
                'descripcion' => 'Terminar mano de obra',
                'destino' => 'Mano Obra Terminada',
                'permiso' => 'terminar_mano_obra',
            ],
        ],
        'Mano Obra Terminada' => [
            'revisar_mano_obra' => [
                'descripcion' => 'Revisar mano de obra',
                'destino' => 'Revision Mano Obra',
                'permiso' => 'revisar_mano_obra',
            ],
        ],
        'Revision Mano Obra' => [
            'aprobar_mano_obra' => [
                'descripcion' => 'Aprobar mano de obra',
                'destino' => 'Mano Obra Aprobada',
                'permiso' => 'aprobar_denegar_mano_obra',
            ],
            'denegar_mano_obra' => [
                'descripcion' => 'Denegar mano de obra',
                'destino' => 'Mano Obra Denegada',
                'permiso' => 'aprobar_denegar_mano_obra',
            ],
        ],
        'Mano Obra Denegada' => [
            'corregir_mano_obra' => [
                'descripcion' => 'Terminar corrección',
                'destino' => 'Mano Obra Terminada',
                'permiso' => 'terminar_mano_obra',
            ],
        ],
        'Mano Obra Aprobada' => [
            'entregar_unidad' => [
                'descripcion' => 'Entregar unidad',
                'destino' => 'Unidad Entregada al Cliente',
                'permiso' => 'entregar_unidad',
            ],
        ],
        'Unidad Entregada al Cliente' => [
            'reingresar_unidad' => [
                'descripcion' => 'Reingresar unidad',
                'destino' => 'Unidad Reingresada al Taller',
                'permiso' => 'reingresar_unidad',
            ],
        ],
        'Unidad Reingresada al Taller' => [
            'reiniciar_diagnostico' => [
                'descripcion' => 'Reiniciar diagnóstico',
                'destino' => 'Diagnostico Iniciado',
                'permiso' => 'iniciar_terminar_diagnostico',
            ],
        ],
    ];

    /**
     * @return array<string, array{descripcion: string, destino: string, permiso: string}>
     */
    public static function acciones(?string $estatus): array
    {
        return self::TRANSICIONES[$estatus ?? ''] ?? [];
    }

    /**
     * @return array{descripcion: string, destino: string, permiso: string}|null
     */
    public static function accion(?string $estatus, string $clave): ?array
    {
        return self::acciones($estatus)[$clave] ?? null;
    }
}
