import { useAuth } from '@/composables/useAuth';
import type { AccionSeguimientoOrdenServicio } from '@/types/generales';

interface AccionSeguimientoProtegida
    extends AccionSeguimientoOrdenServicio {
    permiso: string;
}

const acciones: Record<string, AccionSeguimientoProtegida[]> = {
    '': [
        {
            clave: 'iniciar_diagnostico',
            descripcion: 'Iniciar diagnóstico',
            permiso: 'iniciar_terminar_diagnostico',
        },
    ],
    'Diagnostico Iniciado': [
        {
            clave: 'terminar_diagnostico',
            descripcion: 'Terminar diagnóstico',
            permiso: 'iniciar_terminar_diagnostico',
        },
    ],
    'Diagnostico Terminado': [
        {
            clave: 'terminar_mano_obra',
            descripcion: 'Terminar mano de obra',
            permiso: 'terminar_mano_obra',
        },
    ],
    'Mano Obra Terminada': [
        {
            clave: 'revisar_mano_obra',
            descripcion: 'Revisar mano de obra',
            permiso: 'revisar_mano_obra',
        },
    ],
    'Revision Mano Obra': [
        {
            clave: 'aprobar_mano_obra',
            descripcion: 'Aprobar mano de obra',
            permiso: 'aprobar_denegar_mano_obra',
        },
        {
            clave: 'denegar_mano_obra',
            descripcion: 'Denegar mano de obra',
            permiso: 'aprobar_denegar_mano_obra',
        },
    ],
    'Mano Obra Denegada': [
        {
            clave: 'corregir_mano_obra',
            descripcion: 'Terminar corrección',
            permiso: 'terminar_mano_obra',
        },
    ],
    'Mano Obra Aprobada': [
        {
            clave: 'entregar_unidad',
            descripcion: 'Entregar unidad',
            permiso: 'entregar_unidad',
        },
    ],
    'Unidad Entregada al Cliente': [
        {
            clave: 'reingresar_unidad',
            descripcion: 'Reingresar unidad',
            permiso: 'reingresar_unidad',
        },
    ],
    'Unidad Reingresada al Taller': [
        {
            clave: 'reiniciar_diagnostico',
            descripcion: 'Reiniciar diagnóstico',
            permiso: 'iniciar_terminar_diagnostico',
        },
    ],
};

export const useAccionesSeguimiento = () => {
    const { can } = useAuth();

    const accionesPorEstatus = (
        estatus: string,
    ): AccionSeguimientoOrdenServicio[] => {
        const estatusActual = estatus === 'Sin diagnóstico' ? '' : estatus;

        return (acciones[estatusActual] ?? [])
            .filter((accion) => can(accion.permiso))
            .map(({ clave, descripcion }) => ({
                clave,
                descripcion,
            }));
    };

    return { accionesPorEstatus };
};
