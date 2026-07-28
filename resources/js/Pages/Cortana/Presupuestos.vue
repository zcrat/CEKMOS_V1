<script setup lang="ts">
import Dropdown from '@/components/Zcrat/Elements/Dropdown.vue';
import IconActions from '@/components/Zcrat/Elements/IconActions.vue';
import Table from '@/components/Zcrat/Elements/Table.vue';
import Datapicker from '@/components/Zcrat/Elements/ZDDataPicker.vue';
import empresasselect from '@/components/Zcrat/Filters/empresasselect.vue';
import MultiOptionFilter from '@/components/Zcrat/Filters/MultiOptionFilter.vue';
import Pagination from '@/components/Zcrat/Filters/pagination.vue';
import Button from '@/components/Zcrat/Inputs/Button.vue';
import Search from '@/components/Zcrat/Inputs/Search.vue';
import CambiarModuloOrdenServicioModal from '@/components/Zcrat/modals/CambiarModuloOrdenServicioModal.vue';
import AsignarUsuarioOrdenServicioModal from '@/components/Zcrat/modals/AsignarUsuarioOrdenServicioModal.vue';
import PresupuestoModal from '@/components/Zcrat/modals/PresupuestoModal.vue';
import { useAuth } from '@/composables/useAuth';
import AppLayout from '@/layouts/AppLayout.vue';
import type { AccionEstatusPresupuesto, option, presupuestos } from '@/types/generales';
import type { OrderKeyProp } from '@/types/tablecomponent';
import MyBasicToast from '@/utils/ToastNotificationBasic';
import { ZdAlert } from '@/utils/ZdAlert';
import { useAccionesPresupuesto } from '@/services/presupuesto/acciones';
import { useDebounce } from '@vueuse/core';
import axios from 'axios';
import { computed, ref } from 'vue';

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
const fechas = ref<Date[] | null>(null);
const orderBy = ref<OrderKeyProp | null>(null);
const refreshKey = ref(0);
const showCreate = ref(false);
const showEdit = ref(false);
const showModule = ref(false);
const showUser = ref(false);
const selectedPresupuestoId = ref<number | null>(null);
const selectedOrdenServicioId = ref<number | null>(null);
const selectedModule = ref<option | null>(null);
const selectedUser = ref<option | null>(null);
const prefacturasActive = false;
const { can } = useAuth();
const { accionesPorEstatus } = useAccionesPresupuesto();
const canChangeModule = computed(() => can('cambiar_modulo_presupuestos'));
const canAssignUser = computed(
    () =>
        can('crear_ordenes_servicio')
        || can('cambiar_modulo_presupuestos'),
);
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
        const response = await axios.patch(
            route('presupuesto.estatus.update', row.id),
            { tipo_accion: action.direccion },
        );
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

const ActionPresupuestos = (row: presupuestos) => [
    ...accionesPorEstatus(row.estatus).map((action) => ({
        icon: action.direccion === 'next'
            ? 'fa-solid fa-arrow-right'
            : 'fa-solid fa-arrow-left',
        title: action.descripcion,
        onClick: () => changeStatus(row, action),
        classname: action.direccion === 'next'
            ? 'border-emerald-300 bg-emerald-50 text-emerald-700 hover:border-emerald-500 hover:bg-emerald-100'
            : 'border-amber-300 bg-amber-50 text-amber-700 hover:border-amber-500 hover:bg-amber-100',
    })),
    ...(canChangeModule.value
        ? [{
              icon: 'fa-solid fa-right-left',
              title: 'Cambiar módulo',
              classname: 'border-blue-300 bg-blue-50 text-blue-700 hover:border-blue-500 hover:bg-blue-100',
              onClick: () => openModule(row),
          }]
        : []),
    ...(canAssignUser.value
        ? [{
              icon: 'fa-solid fa-user-gear',
              title: row.user_asignado
                  ? 'Cambiar usuario'
                  : 'Asignar usuario',
              classname: 'border-gray-300 bg-gray-50 text-gray-700 hover:border-gray-500 hover:bg-gray-100',
              onClick: () => openUser(row),
          }]
        : []),
];

const showActionsColumn = computed(
    () => items.value.some((row) => ActionPresupuestos(row).length > 0),
);
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
                    <MultiOptionFilter v-model:selectedIds="modulos" api="select.modulos.disponibles.usuario" label="Módulos" />
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
                    { title: 'Usuario asignado', classname: 'uppercase' },
                    { title: 'Estatus', classname: 'uppercase' },
                    ...(showActionsColumn ? [{ title: 'Acciones', classname: 'uppercase' }] : []),
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
                            { element: escapeHtml(row.usuario_asignado), classname: 'normal-case' },
                            { element: escapeHtml(row.estatus), classname: 'normal-case' },
                            ...(showActionsColumn
                                ? [
                                      {
                                          element: ActionPresupuestos(row).length > 0
                                              ? IconActions
                                              : '-',
                                          props: {
                                              actions: ActionPresupuestos(row),
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

    <PresupuestoModal
        :show="showCreate"
        @close="showCreate = false"
        @saved="refreshItems"
    />
    <PresupuestoModal
        :show="showEdit"
        :presupuesto-id="selectedPresupuestoId"
        @close="showEdit = false"
        @saved="refreshItems"
    />
    <CambiarModuloOrdenServicioModal
        :show="showModule"
        :orden-servicio-id="selectedOrdenServicioId"
        :modulo-actual="selectedModule"
        @close="showModule = false"
        @saved="refreshItems"
    />
    <AsignarUsuarioOrdenServicioModal
        :show="showUser"
        :orden-servicio-id="selectedOrdenServicioId"
        :usuario-actual="selectedUser"
        @close="showUser = false"
        @saved="refreshItems"
    />
</template>
