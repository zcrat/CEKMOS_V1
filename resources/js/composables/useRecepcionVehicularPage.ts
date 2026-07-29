import { useAuth } from '@/composables/useAuth';
import { useAccionesSeguimiento } from '@/services/orden-servicio/acciones';
import type { AccionSeguimientoOrdenServicio, RecepcionesVehiculares } from '@/types/generales';
import MyBasicToast from '@/utils/ToastNotificationBasic';
import { ZdAlert } from '@/utils/ZdAlert';
import { ToggleUploadFiles } from '@/utils/functions/ordenservicio';
import { useEcho } from '@laravel/echo-vue';
import axios from 'axios';
import { computed, ref, type Ref } from 'vue';

type ActionModal = 'taller' | 'modulo' | 'subcontratos' | 'usuario' | 'entrega';

interface IconAction {
    icon?: string;
    text?: string;
    title: string;
    onClick: () => void;
    classname: string;
}

interface DropdownOption {
    label: string;
    onClick: () => void;
    classname: string[];
}

interface ReceptionOptionHandlers {
    openOrder: (id: number) => void;
    openPdf: (id: number) => void;
    openInspection: (id: number) => void;
}

interface DataUpdateWebSocket extends Record<string, unknown> {
    id: number;
}

const statusActionIcons: Record<string, string> = {
    iniciar_diagnostico: 'fa-solid fa-stethoscope',
    terminar_diagnostico: 'fa-solid fa-clipboard-check',
    terminar_mano_obra: 'fa-solid fa-screwdriver-wrench',
    revisar_mano_obra: 'fa-solid fa-magnifying-glass',
    aprobar_mano_obra: 'fa-solid fa-circle-check',
    denegar_mano_obra: 'fa-solid fa-circle-xmark',
    corregir_mano_obra: 'fa-solid fa-screwdriver-wrench',
    entregar_unidad: 'fa-solid fa-car-side',
    reingresar_unidad: 'fa-solid fa-rotate-left',
    reiniciar_diagnostico: 'fa-solid fa-stethoscope',
};

const receptionDateFormatter = new Intl.DateTimeFormat('es-MX', {
    dateStyle: 'medium',
    timeStyle: 'short',
});

const formatDate = (value: string | null) => {
    if (!value) return '-';

    const date = new Date(value);

    return Number.isNaN(date.getTime()) ? '-' : receptionDateFormatter.format(date);
};

export const useRecepcionVehicularPage = (items: Ref<RecepcionesVehiculares[]>, optionHandlers: ReceptionOptionHandlers) => {
    const selectedOrder = ref<RecepcionesVehiculares | null>(null);
    const actionModal = ref<ActionModal | null>(null);
    const refreshKey = ref(0);

    const { can } = useAuth();
    const { accionesPorEstatus } = useAccionesSeguimiento();
    const canManageReception = can('crear_ordenes_servicio');
    const canChangeModule = can('cambiar_modulo_presupuestos');
    const canRollbackStatus = can('retroceder_seguimiento');

    const openAction = (action: ActionModal, order: RecepcionesVehiculares) => {
        selectedOrder.value = order;
        actionModal.value = action;
    };

    const closeAction = () => {
        actionModal.value = null;
    };

    const refreshOrders = () => {
        refreshKey.value++;
    };

    const changeStatus = async (order: RecepcionesVehiculares, action: AccionSeguimientoOrdenServicio) => {
        const confirmed = await ZdAlert({
            title: action.descripcion,
            text: `La orden está en "${order.estatus}". ¿Deseas continuar?`,
            confirmButtonText: action.descripcion,
        });

        if (!confirmed) return;

        try {
            const response = await axios.patch(route('ordenes-servicio.seguimiento.update', order.id), { accion: action.clave });
            MyBasicToast.success(response.data.message);
            refreshOrders();
        } catch (error) {
            MyBasicToast.error(
                axios.isAxiosError(error)
                    ? (error.response?.data?.message ?? 'No fue posible cambiar el estatus')
                    : 'No fue posible cambiar el estatus',
            );
        }
    };

    const rollbackStatus = async (order: RecepcionesVehiculares) => {
        const confirmed = await ZdAlert({
            title: 'Retroceder seguimiento',
            text: `Se eliminará el último estatus de la orden, actualmente "${order.estatus}". ¿Deseas continuar?`,
            confirmButtonText: 'Eliminar último estatus',
        });

        if (!confirmed) return;

        try {
            const response = await axios.delete(route('ordenes-servicio.seguimiento.destroy-last', order.id));
            MyBasicToast.success(response.data.message);
            refreshOrders();
        } catch (error) {
            MyBasicToast.error(
                axios.isAxiosError(error)
                    ? (error.response?.data?.message ?? 'No fue posible retroceder el seguimiento')
                    : 'No fue posible retroceder el seguimiento',
            );
        }
    };

    const accionesRecepcion = (row: RecepcionesVehiculares): IconAction[] => [
        ...(canRollbackStatus && row.tiene_seguimiento
            ? [
                  {
                      text: 'RS',
                      title: 'Retroceder seguimiento',
                      onClick: () => rollbackStatus(row),
                      classname: 'border-red-300 text-red-700 hover:border-red-500 hover:bg-red-50 hover:text-red-800',
                  },
              ]
            : []),
        ...accionesPorEstatus(row.estatus).map((action) => ({
            icon: statusActionIcons[action.clave] ?? 'fa-solid fa-forward-step',
            title: action.descripcion,
            onClick: () => (action.clave === 'entregar_unidad' ? openAction('entrega', row) : changeStatus(row, action)),
            classname: action.clave.includes('denegar')
                ? 'border-red-300 bg-red-50 text-red-700 hover:border-red-500 hover:bg-red-100'
                : 'border-green-300 bg-green-50 text-green-700 hover:border-green-500 hover:bg-green-100',
        })),
        ...(canManageReception
            ? [
                  {
                      icon: 'fa-solid fa-screwdriver-wrench',
                      title: 'Cambiar taller',
                      onClick: () => openAction('taller', row),
                      classname: 'border-amber-300 bg-amber-50 text-amber-700 hover:border-amber-500 hover:bg-amber-100',
                  },
                  {
                      icon: 'fa-solid fa-handshake',
                      title: 'Subcontratos',
                      onClick: () => openAction('subcontratos', row),
                      classname: 'border-violet-300 bg-violet-50 text-violet-700 hover:border-violet-500 hover:bg-violet-100',
                  },
                  {
                      icon: 'fa-solid fa-user-gear',
                      title: row.user_asignado ? 'Cambiar usuario' : 'Asignar usuario',
                      onClick: () => openAction('usuario', row),
                      classname: 'border-gray-300 bg-gray-50 text-gray-700 hover:border-gray-500 hover:bg-gray-100',
                  },
              ]
            : []),
        ...(canChangeModule
            ? [
                  {
                      icon: 'fa-solid fa-right-left',
                      title: 'Cambiar módulo',
                      onClick: () => openAction('modulo', row),
                      classname: 'border-blue-300 bg-blue-50 text-blue-700 hover:border-blue-500 hover:bg-blue-100',
                  },
              ]
            : []),
    ];

    const opcionesRecepcion = (row: RecepcionesVehiculares): DropdownOption[] => [
        {
            label: 'Editar Recepción',
            onClick: () => optionHandlers.openOrder(row.id),
            classname: ['hover:text-gray-800'],
        },
        {
            label: 'Ver PDF',
            onClick: () => optionHandlers.openPdf(row.id),
            classname: ['hover:text-gray-800'],
        },
        {
            label: 'Capturar / editar inspección',
            onClick: () => optionHandlers.openInspection(row.id),
            classname: ['hover:text-gray-800'],
        },
        {
            label: `${row.cambiar_archivos ? 'Desactivar' : 'Activar'} cambios en los archivos`,
            onClick: () => ToggleUploadFiles({ id: row.rv_id, estatus: row.cambiar_archivos }),
            classname: ['hover:text-gray-800'],
        },
    ];

    const showActionsColumn = computed(() => items.value.some((row) => accionesRecepcion(row).length > 0));

    useEcho('ordenes_servicio', '.update', (data: DataUpdateWebSocket) => {
        items.value = items.value.map((item) => (item.id === data.id ? { ...item, ...data } : item));
    });

    useEcho('ordenes_servicio', '.delete', (data: { id: number }) => {
        items.value = items.value.filter((item) => item.id !== data.id);
    });

    return {
        accionesRecepcion,
        actionModal,
        closeAction,
        formatDate,
        opcionesRecepcion,
        refreshKey,
        refreshOrders,
        selectedOrder,
        showActionsColumn,
    };
};
