<script setup lang="ts">
import Dropdown from '@/components/Zcrat/Elements/Dropdown.vue';
import IconActions from '@/components/Zcrat/Elements/IconActions.vue';
import Table from '@/components/Zcrat/Elements/Table.vue';
import Datapicker from '@/components/Zcrat/Elements/ZDDataPicker.vue';
import EmpresasFilter from '@/components/Zcrat/Filters/EmpresasFilter.vue';
import EstatusPresupuestosFilter from '@/components/Zcrat/Filters/EstatusPresupuestosFilter.vue';
import ModulosOrdenServicioFilter from '@/components/Zcrat/Filters/ModulosOrdenServicioFilter.vue';
import UsuarioAsignadoFilter from '@/components/Zcrat/Filters/UsuarioAsignadoFilter.vue';
import Pagination from '@/components/Zcrat/Filters/pagination.vue';
import Button from '@/components/Zcrat/Inputs/Button.vue';
import Search from '@/components/Zcrat/Inputs/Search.vue';
import AsignarUsuarioOrdenServicioModal from '@/components/Zcrat/modals/AsignarUsuarioOrdenServicioModal.vue';
import CambiarModuloOrdenServicioModal from '@/components/Zcrat/modals/CambiarModuloOrdenServicioModal.vue';
import PresupuestoModal from '@/components/Zcrat/modals/PresupuestoModal.vue';
import { escapeHtml, usePresupuestosPage } from '@/composables/usePresupuestosPage';
import AppLayout from '@/layouts/AppLayout.vue';
import type { option, presupuestos } from '@/types/generales';
import type { OrderKeyProp } from '@/types/tablecomponent';
import { useDebounce } from '@vueuse/core';
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
const usuarioAsignado = ref<option | null>(null);
const empresa = ref<option | null>(null);
const fechas = ref<Date[] | null>(null);
const orderBy = ref<OrderKeyProp | null>(null);
const prefacturasActive = false;

const {
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
} = usePresupuestosPage(items);

const params = computed(() => ({
    search: debouncedSearch.value,
    estatus: estatus.value,
    modulos: modulos.value,
    usuario_asignado: usuarioAsignado.value?.value ?? null,
    empresa_id: empresa.value?.value ?? null,
    fechas: fechas.value?.length === 2 ? fechas.value.map((date) => date.toISOString()) : null,
    orderBy: orderBy.value,
}));

const hasFilters = computed(
    () =>
        debouncedSearch.value.trim() !== '' ||
        estatus.value.length > 0 ||
        modulos.value.length > 0 ||
        usuarioAsignado.value !== null ||
        empresa.value !== null ||
        Boolean(fechas.value?.length),
);

const emptyMessage = computed(() =>
    hasFilters.value ? 'No se encontraron presupuestos con los filtros seleccionados' : 'No hay presupuestos para mostrar',
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
                    <EmpresasFilter v-model="empresa" />
                    <EstatusPresupuestosFilter v-model="estatus" />
                    <ModulosOrdenServicioFilter v-model="modulos" />
                    <UsuarioAsignadoFilter v-model="usuarioAsignado" />
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
                            { element: formatDate(row.creacion), classname: 'whitespace-nowrap' },
                            { element: escapeHtml(row.usuario_asignado), classname: 'normal-case' },
                            { element: escapeHtml(row.estatus), classname: 'normal-case' },
                            ...(showActionsColumn
                                ? [
                                      {
                                          element: accionesPresupuesto(row).length > 0 ? IconActions : '-',
                                          props: {
                                              actions: accionesPresupuesto(row),
                                          },
                                      },
                                  ]
                                : []),
                            {
                                element: Dropdown,
                                props: {
                                    triggerText: 'Opciones',
                                    options: opcionesPresupuesto(row),
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

    <PresupuestoModal :show="showCreate" @close="showCreate = false" @saved="refreshItems" />
    <PresupuestoModal :show="showEdit" :presupuesto-id="selectedPresupuestoId" @close="showEdit = false" @saved="refreshItems" />
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
