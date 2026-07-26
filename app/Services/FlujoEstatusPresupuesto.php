<?php

namespace App\Services;

final class FlujoEstatusPresupuesto
{
    /**
     * @var array<string, array<string, array{descripcion: string, destino: string, permiso: string}>>
     */
    private const TRANSICIONES = [
        'Pendiente De Enviar' => [
            'next' => [
                'descripcion' => 'Enviar a autorización',
                'destino' => 'Pendiente De Autorizar',
                'permiso' => 'autorizar_presupuestos',
            ],
        ],
        'Pendiente De Autorizar' => [
            'next' => [
                'descripcion' => 'Aprobar autorización',
                'destino' => 'Autorizacion Aprobada',
                'permiso' => 'aprobar_presupuestos',
            ],
            'back' => [
                'descripcion' => 'Denegar autorización',
                'destino' => 'Autorizacion Denegada',
                'permiso' => 'aprobar_presupuestos',
            ],
        ],
        'Autorizacion Aprobada' => [
            'next' => [
                'descripcion' => 'Enviar a pago',
                'destino' => 'Pendiente De Pago',
                'permiso' => 'pagar_presupuestos',
            ],
            'back' => [
                'descripcion' => 'Regresar a autorización',
                'destino' => 'Pendiente De Autorizar',
                'permiso' => 'aprobar_presupuestos',
            ],
        ],
        'Pendiente De Pago' => [
            'next' => [
                'descripcion' => 'Aprobar pago',
                'destino' => 'Pendiente De Terminar',
                'permiso' => 'pagar_presupuestos',
            ],
            'back' => [
                'descripcion' => 'Denegar pago',
                'destino' => 'Pago Denegado',
                'permiso' => 'pagar_presupuestos',
            ],
        ],
        'Pendiente De Terminar' => [
            'next' => [
                'descripcion' => 'Terminar con factura',
                'destino' => 'Terminado Con Factura',
                'permiso' => 'facturar_presupuestos',
            ],
            'back' => [
                'descripcion' => 'Terminar sin factura',
                'destino' => 'Solo Terminado',
                'permiso' => 'terminar_presupuestos',
            ],
        ],
        'Terminado Con Factura' => [
            'back' => [
                'descripcion' => 'Reabrir terminación',
                'destino' => 'Pendiente De Terminar',
                'permiso' => 'terminar_presupuestos',
            ],
        ],
        'Autorizacion Denegada' => [
            'next' => [
                'descripcion' => 'Reenviar a autorización',
                'destino' => 'Pendiente De Autorizar',
                'permiso' => 'autorizar_presupuestos',
            ],
            'back' => [
                'descripcion' => 'Regresar a pendiente de envío',
                'destino' => 'Pendiente De Enviar',
                'permiso' => 'autorizar_presupuestos',
            ],
        ],
        'Pago Denegado' => [
            'next' => [
                'descripcion' => 'Reenviar a pago',
                'destino' => 'Pendiente De Pago',
                'permiso' => 'pagar_presupuestos',
            ],
            'back' => [
                'descripcion' => 'Regresar a autorización aprobada',
                'destino' => 'Autorizacion Aprobada',
                'permiso' => 'aprobar_presupuestos',
            ],
        ],
        'Solo Terminado' => [
            'next' => [
                'descripcion' => 'Agregar factura y terminar',
                'destino' => 'Terminado Con Factura',
                'permiso' => 'facturar_presupuestos',
            ],
            'back' => [
                'descripcion' => 'Reabrir terminación',
                'destino' => 'Pendiente De Terminar',
                'permiso' => 'terminar_presupuestos',
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
    public static function accion(?string $estatus, string $direccion): ?array
    {
        return self::acciones($estatus)[$direccion] ?? null;
    }
}
