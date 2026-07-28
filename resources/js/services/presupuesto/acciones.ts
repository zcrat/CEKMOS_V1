import { useAuth } from '@/composables/useAuth';
import type { AccionEstatusPresupuesto } from '@/types/generales';

interface TransicionPresupuesto extends AccionEstatusPresupuesto {
    permiso: string;
}

const transiciones: Record<string, TransicionPresupuesto[]> = {
    'Pendiente De Enviar': [
        {
            direccion: 'next',
            descripcion: 'Enviar a autorización',
            permiso: 'autorizar_presupuestos',
        },
    ],
    'Pendiente De Autorizar': [
        {
            direccion: 'next',
            descripcion: 'Aprobar autorización',
            permiso: 'aprobar_presupuestos',
        },
        {
            direccion: 'back',
            descripcion: 'Denegar autorización',
            permiso: 'aprobar_presupuestos',
        },
    ],
    'Autorizacion Aprobada': [
        {
            direccion: 'next',
            descripcion: 'Enviar a pago',
            permiso: 'pagar_presupuestos',
        },
        {
            direccion: 'back',
            descripcion: 'Regresar a autorización',
            permiso: 'aprobar_presupuestos',
        },
    ],
    'Pendiente De Pago': [
        {
            direccion: 'next',
            descripcion: 'Aprobar pago',
            permiso: 'pagar_presupuestos',
        },
        {
            direccion: 'back',
            descripcion: 'Denegar pago',
            permiso: 'pagar_presupuestos',
        },
    ],
    'Pendiente De Terminar': [
        {
            direccion: 'next',
            descripcion: 'Terminar con factura',
            permiso: 'facturar_presupuestos',
        },
        {
            direccion: 'back',
            descripcion: 'Terminar sin factura',
            permiso: 'terminar_presupuestos',
        },
    ],
    'Terminado Con Factura': [
        {
            direccion: 'back',
            descripcion: 'Reabrir terminación',
            permiso: 'terminar_presupuestos',
        },
    ],
    'Autorizacion Denegada': [
        {
            direccion: 'next',
            descripcion: 'Reenviar a autorización',
            permiso: 'autorizar_presupuestos',
        },
        {
            direccion: 'back',
            descripcion: 'Regresar a pendiente de envío',
            permiso: 'autorizar_presupuestos',
        },
    ],
    'Pago Denegado': [
        {
            direccion: 'next',
            descripcion: 'Reenviar a pago',
            permiso: 'pagar_presupuestos',
        },
        {
            direccion: 'back',
            descripcion: 'Regresar a autorización aprobada',
            permiso: 'aprobar_presupuestos',
        },
    ],
    'Solo Terminado': [
        {
            direccion: 'next',
            descripcion: 'Agregar factura y terminar',
            permiso: 'facturar_presupuestos',
        },
        {
            direccion: 'back',
            descripcion: 'Reabrir terminación',
            permiso: 'terminar_presupuestos',
        },
    ],
};

export const useAccionesPresupuesto = () => {
    const { can } = useAuth();

    const accionesPorEstatus = (
        estatus: string,
    ): AccionEstatusPresupuesto[] =>
        (transiciones[estatus] ?? [])
            .filter((accion) => can(accion.permiso))
            .map(({ direccion, descripcion }) => ({
                direccion,
                descripcion,
            }));

    return { accionesPorEstatus };
};
