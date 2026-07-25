<script setup lang="ts">
import Dropdown from '@/components/Zcrat/Elements/Dropdown.vue';
import Table from '@/components/Zcrat/Elements/Table.vue';
import ZDRemoteSelect from '@/components/Zcrat/Elements/ZDRemoteSelect.vue';
import MultiOptionFilter from '@/components/Zcrat/Filters/MultiOptionFilter.vue';
import Pagination from '@/components/Zcrat/Filters/pagination.vue';
import Button from '@/components/Zcrat/Inputs/Button.vue';
import Search from '@/components/Zcrat/Inputs/Search.vue';
import ConceptoPresupuestoModal from '@/components/Zcrat/modals/ConceptoPresupuestoModal.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import type { option } from '@/types/generales';
import MyBasicToast from '@/utils/ToastNotificationBasic';
import { ZdAlert } from '@/utils/ZdAlert';
import { useAuth } from '@/composables/useAuth';
import { useDebounce } from '@vueuse/core';
import axios from 'axios';
import { computed, ref } from 'vue';

const { can, canAny } = useAuth();

interface ConceptoPresupuesto {
    id: number;
    concepto_id: number;
    descripcion: string;
    modulo: string;
    categoria_sat: string;
    codigo_sat: string;
    unidad_sat: string;
    codigo_unidad_sat: string;
    tipo: string;
    vehiculo: string;
    usuario: string;
    total: string | number | null;
}

const currentPage = ref(1);
const itemsPerPage = ref(10);
const totalItems = ref(0);
const items = ref<ConceptoPresupuesto[]>([]);
const loading = ref(true);
const search = ref('');
const debouncedSearch = useDebounce(search, 400);
const refreshKey = ref(0);
const showModal = ref(false);
const selectedCostoId = ref<number | null>(null);
const categoriaSat = ref<option | null>(null);
const unidadSat = ref<option | null>(null);
const vehiculo = ref<option | null>(null);
const categoria = ref<option | null>(null);
const modulos = ref<(number | string)[]>([]);

const params = computed(() => ({
    search: debouncedSearch.value,
    categoria_sat: categoriaSat.value?.value ?? null,
    unidad_sat: unidadSat.value?.value ?? null,
    vehiculo: vehiculo.value?.value ?? null,
    categoria: categoria.value?.value ?? null,
    modulos: modulos.value,
}));

const escapeHtml = (value: string | number | null) =>
    String(value ?? '')
        .replaceAll('&', '&amp;')
        .replaceAll('<', '&lt;')
        .replaceAll('>', '&gt;')
        .replaceAll('"', '&quot;')
        .replaceAll("'", '&#039;');

const formatCurrency = (value: string | number | null) => {
    const amount = Number(value);

    return new Intl.NumberFormat('es-MX', {
        style: 'currency',
        currency: 'MXN',
    }).format(Number.isFinite(amount) ? amount : 0);
};

const openCreate = () => {
    selectedCostoId.value = null;
    showModal.value = true;
};

const openEdit = (id: number) => {
    selectedCostoId.value = id;
    showModal.value = true;
};

const refreshItems = () => {
    refreshKey.value++;
};

const deleteConcepto = async (row: ConceptoPresupuesto) => {
    const confirmed = await ZdAlert({
        title: 'Eliminar concepto',
        text: `¿Deseas eliminar "${row.descripcion}"?`,
        confirmButtonText: 'Eliminar',
    });

    if (!confirmed) return;

    try {
        await axios.delete(route('catalogos.conceptos.destroy', row.id));
        MyBasicToast.success('Concepto eliminado correctamente');
        refreshItems();
    } catch {
        MyBasicToast.error('No fue posible eliminar el concepto');
    }
};
</script>

<template>
    <AppLayout title="Conceptos" description="Catálogo de conceptos" :loading="loading" messageLoading="Cargando conceptos">
        <template #header>
            <Button
                v-if="can('crear_catalogo_conceptos')"
                text="Nuevo concepto"
                type="save"
                icon="fa-solid fa-circle-plus"
                @click="openCreate"
            />
        </template>

        <template #filtering>
            <div class="flex w-full flex-col gap-3">
                <div class="flex w-full flex-wrap items-end gap-2">
                    <Search v-model="search" Classdiv="w-full sm:w-[22rem]" placeholder="Buscar por descripción del concepto" />
                    <ZDRemoteSelect
                        v-model="categoriaSat"
                        endpoint="select2.catalogo.categorias-sat"
                        label="Categoría SAT"
                        placeholder="Descripción o código SAT"
                        classDiv="w-full sm:w-[16rem]"
                        :cacheoptions="false"
                    />
                    <ZDRemoteSelect
                        v-model="unidadSat"
                        endpoint="select2.catalogo.unidades-sat"
                        label="Unidad SAT"
                        placeholder="Descripción o código"
                        classDiv="w-full sm:w-[16rem]"
                        :cacheoptions="false"
                    />
                    <ZDRemoteSelect
                        v-model="vehiculo"
                        endpoint="select2.catalogo.vehiculos-conceptos"
                        label="Vehículo"
                        placeholder="Buscar vehículo"
                        classDiv="w-full sm:w-[16rem]"
                        :cacheoptions="false"
                    />
                    <ZDRemoteSelect
                        v-model="categoria"
                        endpoint="select2.catalogo.categorias-conceptos"
                        label="Categoría"
                        placeholder="Buscar categoría"
                        classDiv="w-full sm:w-[16rem]"
                        :cacheoptions="false"
                    />
                    <div class="w-full pb-0.5 sm:w-[16rem]">
                        <MultiOptionFilter v-model:selectedIds="modulos" api="select.modulos.disponibles.usuario" label="Módulos" />
                    </div>
                </div>

                <Pagination
                    :key="refreshKey"
                    api="catalogos.conceptos.read"
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
                classname="tabla mt-2"
                :titles="[
                    'Descripción',
                    'Módulo',
                    {
                        title: 'Categoría SAT',
                        subtittles: [{ title: 'Descripción' }, { title: 'Código' }],
                    },
                    {
                        title: 'Unidad SAT',
                        subtittles: [{ title: 'Descripción' }, { title: 'Código' }],
                    },
                    'Categoría',
                    'Vehículo',
                    'Usuario',
                    'Total',
                    ...(canAny(['editar_catalogo_conceptos', 'eliminar_catalogo_conceptos'])
                        ? ['Acciones']
                        : []),
                ]"
                :rows="
                    items.map((row) => ({
                        classname: 'bg-grey-300',
                        columns: [
                            { element: escapeHtml(row.descripcion), classname: 'normal-case' },
                            { element: escapeHtml(row.modulo), classname: 'normal-case' },
                            { element: escapeHtml(row.categoria_sat), classname: 'normal-case' },
                            { element: escapeHtml(row.codigo_sat), classname: 'uppercase' },
                            { element: escapeHtml(row.unidad_sat), classname: 'normal-case' },
                            { element: escapeHtml(row.codigo_unidad_sat), classname: 'uppercase' },
                            { element: escapeHtml(row.tipo), classname: 'normal-case' },
                            { element: escapeHtml(row.vehiculo), classname: 'normal-case' },
                            { element: escapeHtml(row.usuario), classname: 'normal-case' },
                            { element: formatCurrency(row.total), classname: 'whitespace-nowrap text-right' },
                            ...(canAny(['editar_catalogo_conceptos', 'eliminar_catalogo_conceptos'])
                                ? [{
                                    element: Dropdown,
                                    props: {
                                        triggerText: 'Acciones',
                                        options: [
                                            ...(can('editar_catalogo_conceptos')
                                                ? [{
                                                    label: 'Modificar',
                                                    onClick: () => openEdit(row.id),
                                                    classname: ['hover:text-blue-700'],
                                                }]
                                                : []),
                                            ...(can('eliminar_catalogo_conceptos')
                                                ? [{
                                                    label: 'Eliminar',
                                                    onClick: () => deleteConcepto(row),
                                                    classname: ['hover:text-red-700'],
                                                }]
                                                : []),
                                        ],
                                        contentClasses: { bg: 'bg-white' },
                                    },
                                }]
                                : []),
                        ],
                    }))
                "
            />

            <div v-else class="flex flex-1 items-center justify-center">
                <span class="text-lg">No hay conceptos para mostrar</span>
            </div>
        </template>
    </AppLayout>

    <ConceptoPresupuestoModal :show="showModal" :costo-id="selectedCostoId" @close="showModal = false" @saved="refreshItems" />
</template>
