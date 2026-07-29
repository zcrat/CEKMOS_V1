import { useAuth } from '@/composables/useAuth';
import { useAccionesPresupuesto } from '@/services/presupuesto/acciones';
import type { AccionEstatusPresupuesto, option, presupuestos } from '@/types/generales';
import MyBasicToast from '@/utils/ToastNotificationBasic';
import { ZdAlert } from '@/utils/ZdAlert';
import axios from 'axios';
import { computed, ref, type Ref } from 'vue';

interface IconAction {
    icon: string;
    title: string;
    onClick: () => void;
    classname: string;
}

interface DropdownOption {
    label: string;
    onClick: () => void;
    classname: string[];
}

export const escapeHtml = (value: string | number | null) =>
    String(value ?? '')
        .replaceAll('&', '&amp;')
        .replaceAll('<', '&lt;')
        .replaceAll('>', '&gt;')
        .replaceAll('"', '&quot;')
        .replaceAll("'", '&#039;');

const presupuestoDateFormatter = new Intl.DateTimeFormat('es-MX', {
    dateStyle: 'medium',
    timeStyle: 'short',
});

const formatDate = (value: string | null) => {
    if (!value) return '-';

    const date = new Date(value);

    return Number.isNaN(date.getTime()) ? '-' : presupuestoDateFormatter.format(date);
};

export const usePresupuestosPage = (items: Ref<presupuestos[]>) => {
    const refreshKey = ref(0);
    const showCreate = ref(false);
    const showEdit = ref(false);
    const showModule = ref(false);
    const showUser = ref(false);
    const selectedPresupuestoId = ref<number | null>(null);
    const selectedOrdenServicioId = ref<number | null>(null);
    const selectedModule = ref<option | null>(null);
    const selectedUser = ref<option | null>(null);

    const { can } = useAuth();
    const { accionesPorEstatus } = useAccionesPresupuesto();
    const canChangeModule = computed(() => can('cambiar_modulo_presupuestos'));
    const canAssignUser = computed(() => can('crear_ordenes_servicio') || can('cambiar_modulo_presupuestos'));

    const refreshItems = () => {
        refreshKey.value++;
    };

    const openEdit = (id: number) => {
        selectedPresupuestoId.value = id;
        showEdit.value = true;
    };

    const openModule = (row: presupuestos) => {
        selectedOrdenServicioId.value = row.orden_id;
        selectedModule.value = {
            value: row.modulo_id,
            label: row.modulo,
        };
        showModule.value = true;
    };

    const openUser = (row: presupuestos) => {
        selectedOrdenServicioId.value = row.orden_id;
        selectedUser.value = row.user_asignado
            ? {
                  value: row.user_asignado,
                  label: row.usuario_asignado,
              }
            : null;
        showUser.value = true;
    };

    const deletePresupuesto = async (row: presupuestos) => {
        const confirmed = await ZdAlert({
            title: 'Eliminar presupuesto',
            text: `¿Deseas eliminar el presupuesto "${row.folio}"?`,
            confirmButtonText: 'Eliminar',
        });

        if (!confirmed) return;

        try {
            const response = await axios.delete(route('presupuesto.destroy', row.id));
            MyBasicToast.success(response.data.message);
            refreshItems();
        } catch {
            MyBasicToast.error('No fue posible eliminar el presupuesto');
        }
    };

    const changeStatus = async (row: presupuestos, action: AccionEstatusPresupuesto) => {
        const confirmed = await ZdAlert({
            title: action.descripcion,
            text: `El presupuesto "${row.folio}" está en "${row.estatus}". ¿Deseas continuar?`,
            confirmButtonText: action.descripcion,
        });

        if (!confirmed) return;

        try {
            const response = await axios.patch(route('presupuesto.estatus.update', row.id), { tipo_accion: action.direccion });
            MyBasicToast.success(response.data.message);
            refreshItems();
        } catch (error) {
            if (axios.isAxiosError(error)) {
                MyBasicToast.error(error.response?.data?.message ?? 'No fue posible actualizar el estado');
            } else {
                MyBasicToast.error('No fue posible actualizar el estado');
            }
        }
    };

    const accionesPresupuesto = (row: presupuestos): IconAction[] => [
        ...accionesPorEstatus(row.estatus).map((action) => ({
            icon: action.direccion === 'next' ? 'fa-solid fa-arrow-right' : 'fa-solid fa-arrow-left',
            title: action.descripcion,
            onClick: () => changeStatus(row, action),
            classname:
                action.direccion === 'next'
                    ? 'border-emerald-300 bg-emerald-50 text-emerald-700 hover:border-emerald-500 hover:bg-emerald-100'
                    : 'border-amber-300 bg-amber-50 text-amber-700 hover:border-amber-500 hover:bg-amber-100',
        })),
        ...(canChangeModule.value
            ? [
                  {
                      icon: 'fa-solid fa-right-left',
                      title: 'Cambiar módulo',
                      classname: 'border-blue-300 bg-blue-50 text-blue-700 hover:border-blue-500 hover:bg-blue-100',
                      onClick: () => openModule(row),
                  },
              ]
            : []),
        ...(canAssignUser.value
            ? [
                  {
                      icon: 'fa-solid fa-user-gear',
                      title: row.user_asignado ? 'Cambiar usuario' : 'Asignar usuario',
                      classname: 'border-gray-300 bg-gray-50 text-gray-700 hover:border-gray-500 hover:bg-gray-100',
                      onClick: () => openUser(row),
                  },
              ]
            : []),
    ];

    const opcionesPresupuesto = (row: presupuestos): DropdownOption[] => [
        {
            label: 'Modificar',
            onClick: () => openEdit(row.id),
            classname: ['hover:text-blue-700'],
        },
        {
            label: 'Eliminar',
            onClick: () => deletePresupuesto(row),
            classname: ['hover:text-red-700'],
        },
    ];

    const showActionsColumn = computed(() => items.value.some((row) => accionesPresupuesto(row).length > 0));

    return {
        accionesPresupuesto,
        formatDate,
        opcionesPresupuesto,
        refreshItems,
        refreshKey,
        selectedModule,
        selectedOrdenServicioId,
        selectedPresupuestoId,
        selectedUser,
        showActionsColumn,
        showCreate,
        showEdit,
        showModule,
        showUser,
    };
};
