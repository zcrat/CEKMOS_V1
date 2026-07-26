<script setup lang="ts">
import Dropdown from '@/components/Zcrat/Elements/Dropdown.vue';
import Table from '@/components/Zcrat/Elements/Table.vue';
import Datapicker from '@/components/Zcrat/Elements/ZDDataPicker.vue';
import empresasselect from '@/components/Zcrat/Filters/empresasselect.vue';
import MultiOptionFilter from '@/components/Zcrat/Filters/MultiOptionFilter.vue';
import Pagination from '@/components/Zcrat/Filters/pagination.vue';
import Button from '@/components/Zcrat/Inputs/Button.vue';
import Search from '@/components/Zcrat/Inputs/Search.vue';
import CambiarModuloOrdenServicioModal from '@/components/Zcrat/modals/CambiarModuloOrdenServicioModal.vue';
import Nuevo from '@/components/Zcrat/modals/CreatePresupuesto.vue';
import EditarPresupuestoModal from '@/components/Zcrat/modals/EditarPresupuestoModal.vue';
import { useAuth } from '@/composables/useAuth';
import AppLayout from '@/layouts/AppLayout.vue';
import type { AccionEstatusPresupuesto, option, presupuestos } from '@/types/generales';
import type { OrderKeyProp } from '@/types/tablecomponent';
import MyBasicToast from '@/utils/ToastNotificationBasic';
import { ZdAlert } from '@/utils/ZdAlert';
import { useDebounce } from '@vueuse/core';
import axios from 'axios';
import { computed, defineComponent, h, type PropType, ref } from 'vue';

const currentPage = ref(1);
const itemsPerPage = ref(10);
const totalItems = ref(0);
const items = ref<presupuestos[]>([]);
const loading = ref(true);
const search = ref('');
const debouncedSearch = useDebounce(search, 400);
const estatus = ref<(number | string)[]>([]);
const modulos = ref<(number | string)[]>([]);
const empresa = ref<string | null>(null);
const fechas = ref<any>();
const orderBy = ref<OrderKeyProp | null>(null);
const refreshKey = ref(0);
const showCreate = ref(false);
const showEdit = ref(false);
const showModule = ref(false);
const selectedPresupuestoId = ref<number | null>(null);
const selectedOrdenServicioId = ref<number | null>(null);
const selectedModule = ref<option | null>(null);
const prefacturasActive = false;
const { can, canAny } = useAuth();
const canManageStatus = computed(() =>
    canAny([
        'autorizar_presupuestos',
        'aprobar_presupuestos',
        'pagar_presupuestos',
        'terminar_presupuestos',
        'facturar_presupuestos',
    ]),
);
const canChangeModule = computed(() => can('cambiar_modulo_presupuestos'));

const BudgetActions = defineComponent({
    props: {
        actions: {
            type: Array as PropType<AccionEstatusPresupuesto[]>,
            required: true,
        },
        onAction: {
            type: Function as PropType<(action: AccionEstatusPresupuesto) => void>,
            required: true,
        },
        canChangeModule: {
            type: Boolean,
            required: true,
        },
        onChangeModule: {
            type: Function as PropType<() => void>,
            required: true,
        },
    },
    setup(componentProps) {
        return () => {
            const statusButtons = componentProps.actions.map((action) =>
                h(Button, {
                    key: action.direccion,
                    text: action.descripcion,
                    type: action.direccion,
                    size: 'compact',
                    classname: 'whitespace-nowrap',
                    onClick: () => componentProps.onAction(action),
                }),
            );
            const actionGroups = statusButtons.length > 0
                ? [
                      h(
                          'div',
                          { class: 'flex flex-col gap-1.5' },
                          statusButtons,
                      ),
                  ]
                : [];

            if (componentProps.canChangeModule) {
                actionGroups.push(
                    h(Button, {
                        key: 'change-module',
                        icon: 'fa-solid fa-right-left',
                        type: 'module',
                        size: 'icon',
                        title: 'Cambiar módulo',
                        ariaLabel: 'Cambiar módulo de la orden',
                        onClick: componentProps.onChangeModule,
                    }),
                );
            }

            return h(
                'div',
                { class: 'flex min-w-max flex-row items-center justify-center gap-2 whitespace-nowrap px-1' },
                actionGroups.length > 0
                    ? actionGroups
                    : [h('span', { class: 'text-sm text-gray-500' }, 'Sin acciones disponibles')],
            );
        };
    },
});

const escapeHtml = (value: string | number | null) =>
    String(value ?? '')
        .replaceAll('&', '&amp;')
        .replaceAll('<', '&lt;')
        .replaceAll('>', '&gt;')
        .replaceAll('"', '&quot;')
        .replaceAll("'", '&#039;');

const params = computed(() => ({
    search: debouncedSearch.value,
    estatus: estatus.value,
    modulos: modulos.value,
    empresa_id: empresa.value || null,
    fechas: fechas.value?.length === 2 ? fechas.value.map((date) => date.toISOString()) : null,
    orderBy: orderBy.value,
}));

const hasFilters = computed(
    () =>
        debouncedSearch.value.trim() !== '' ||
        estatus.value.length > 0 ||
        modulos.value.length > 0 ||
        Boolean(empresa.value) ||
        Boolean(fechas.value?.length),
);

const emptyMessage = computed(() =>
    hasFilters.value ? 'No se encontraron presupuestos con los filtros seleccionados' : 'No hay presupuestos para mostrar',
);

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

const refreshItems = () => {
    refreshKey.value++;
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

const changeStatus = async (
    row: presupuestos,
    action: AccionEstatusPresupuesto,
) => {
    const confirmed = await ZdAlert({
        title: action.descripcion,
        text: `El presupuesto "${row.folio}" está en "${row.estatus}". ¿Deseas continuar?`,
        confirmButtonText: action.descripcion,
    });

    if (!confirmed) return;

    try {
        const routeName = action.direccion === 'next'
            ? 'presupuesto.estatus.next'
            : 'presupuesto.estatus.back';
        const response = await axios.patch(route(routeName, row.id));
        MyBasicToast.success(response.data.message);
        refreshItems();
    } catch (error) {
        if (axios.isAxiosError(error)) {
            MyBasicToast.error(
                error.response?.data?.message ?? 'No fue posible actualizar el estado',
            );
        } else {
            MyBasicToast.error('No fue posible actualizar el estado');
        }
    }
};
</script>

<template>
    <AppLayout title="Presupuestos" description="Consulta y administración de presupuestos" :loading="loading">
        <template #header>
            <Button text="Exportar" />
            <Button text="Nueva" @click="showCreate = true" />
            <Button text="Prefacturas" :classname="prefacturasActive ? 'bg-red-700' : 'bg-blue-700'" />
        </template>

        <template #filtering>
            <div class="flex w-full flex-col gap-3">
                <div class="flex w-full flex-wrap items-end gap-2">
                    <Search v-model="search" Classdiv="w-full sm:w-[30rem]" placeholder="Buscar por folio, placas, económico u orden de servicio" />
                    <empresasselect v-model="empresa" :can-new="false" />
                    <MultiOptionFilter v-model:selectedIds="estatus" api="select.status" :params="{ categoria_id: 2 }" label="Estatus" />
                    <MultiOptionFilter v-model:selectedIds="modulos" api="select.presupuestos.modulos" label="Módulos" />
                    <Datapicker v-model="fechas" label="Fecha de creación" />
                </div>

                <Pagination
                    :key="refreshKey"
                    api="cortana.presupuesto.items"
                    :params="params"
                    v-model:currentPage="currentPage"
                    v-model:itemsPerPage="itemsPerPage"
                    v-model:totalItems="totalItems"
                    v-model:items="items"
                    v-model:loading="loading"
                    changesItems
                />
            </div>
        </template>

        <template #content>
            <Table
                v-if="items.length > 0"
                v-model:OrderKey="orderBy"
                :titles="[
                    { title: 'Folio', classname: 'uppercase', CanOrder: { key: 'folio', types: 'ambos' } },
                    { title: 'No. Orden', classname: 'uppercase', CanOrder: { key: 'orden', types: 'ambos' } },
                    { title: 'Empresa', classname: 'uppercase', CanOrder: { key: 'empresa', types: 'ambos' } },
                    { title: 'Módulo', classname: 'uppercase' },
                    { title: 'Económico', classname: 'uppercase' },
                    { title: 'Placas', classname: 'uppercase' },
                    { title: 'VIN', classname: 'uppercase' },
                    { title: 'Creación', classname: 'uppercase', CanOrder: { key: 'creacion', types: 'ambos' } },
                    { title: 'Estatus', classname: 'uppercase' },
                    ...(canManageStatus || canChangeModule ? [{ title: 'Acciones', classname: 'uppercase' }] : []),
                    { title: 'Opciones', classname: 'uppercase' },
                ]"
                :rows="
                    items.map((row) => ({
                        classname: 'bg-grey-300',
                        columns: [
                            { element: escapeHtml(row.folio), classname: 'normal-case' },
                            { element: escapeHtml(row.orden), classname: 'uppercase' },
                            { element: escapeHtml(row.empresa), classname: 'normal-case' },
                            { element: escapeHtml(row.modulo), classname: 'normal-case' },
                            { element: escapeHtml(row.economico), classname: 'uppercase' },
                            { element: escapeHtml(row.placas), classname: 'uppercase' },
                            { element: escapeHtml(row.vin), classname: 'uppercase' },
                            { element: escapeHtml(row.creacion), classname: 'whitespace-nowrap' },
                            { element: escapeHtml(row.estatus), classname: 'normal-case' },
                            ...(canManageStatus || canChangeModule
                                ? [
                                      {
                                          element: BudgetActions,
                                          props: {
                                              actions: row.acciones_estatus,
                                              onAction: (action: AccionEstatusPresupuesto) =>
                                                  changeStatus(row, action),
                                              canChangeModule,
                                              onChangeModule: () => openModule(row),
                                          },
                                      },
                                  ]
                                : []),
                            {
                                element: Dropdown,
                                props: {
                                    triggerText: 'Opciones',
                                    options: [
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
                                    ],
                                    contentClasses: { bg: 'bg-white' },
                                },
                            },
                        ],
                    }))
                "
                classname="tabla mt-2"
            />

            <div v-else-if="!loading" class="flex flex-1 items-center justify-center">
                <span class="text-lg">{{ emptyMessage }}</span>
            </div>
        </template>
    </AppLayout>

    <Nuevo v-model:show="showCreate" @close="showCreate = false" />
    <EditarPresupuestoModal :show="showEdit" :presupuesto-id="selectedPresupuestoId" @close="showEdit = false" @saved="refreshItems" />
    <CambiarModuloOrdenServicioModal
        :show="showModule"
        :orden-servicio-id="selectedOrdenServicioId"
        :modulo-actual="selectedModule"
        @close="showModule = false"
        @saved="refreshItems"
    />
</template>
