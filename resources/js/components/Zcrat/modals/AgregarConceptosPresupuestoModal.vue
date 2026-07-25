<script setup lang="ts">
import Table from '@/components/Zcrat/Elements/Table.vue';
import MultiOptionFilter from '@/components/Zcrat/Filters/MultiOptionFilter.vue';
import Pagination from '@/components/Zcrat/Filters/pagination.vue';
import Search from '@/components/Zcrat/Inputs/Search.vue';
import BaseModal from '@/components/Zcrat/modals/BaseModal.vue';
import type { buttonconfirmed } from '@/types/modals';
import MyBasicToast from '@/utils/ToastNotificationBasic';
import { useDebounce } from '@vueuse/core';
import axios from 'axios';
import { computed, defineComponent, h, ref, watch } from 'vue';

interface ConceptoDisponible {
    id: number;
    descripcion: string;
    categoria: string;
    fijo: boolean;
    vehiculo: string;
    total: string | number;
}

const props = defineProps<{
    show: boolean;
    presupuestoId: number | null;
}>();

const emit = defineEmits<{
    (event: 'close'): void;
    (event: 'added'): void;
}>();

const currentPage = ref(1);
const itemsPerPage = ref(10);
const totalItems = ref(0);
const items = ref<ConceptoDisponible[]>([]);
const loading = ref(false);
const saving = ref(false);
const search = ref('');
const debouncedSearch = useDebounce(search, 400);
const categorias = ref<(number | string)[]>([]);
const selectedIds = ref<number[]>([]);
const refreshKey = ref(0);

const escapeHtml = (value: string | number | null) =>
    String(value ?? '')
        .replaceAll('&', '&amp;')
        .replaceAll('<', '&lt;')
        .replaceAll('>', '&gt;')
        .replaceAll('"', '&quot;')
        .replaceAll("'", '&#039;');

const SelectionCheckbox = defineComponent({
    props: {
        id: { type: Number, required: true },
        checked: { type: Boolean, required: true },
    },
    emits: ['toggle'],
    setup(componentProps, { emit: componentEmit }) {
        return () =>
            h('input', {
                type: 'checkbox',
                checked: componentProps.checked,
                class: 'h-4 w-4 cursor-pointer rounded border-gray-300 text-blue-600',
                onChange: () => componentEmit('toggle', componentProps.id),
            });
    },
});

const params = computed(() => ({
    presupuesto_id: props.presupuestoId,
    search: debouncedSearch.value,
    categorias: categorias.value,
}));

const hasFilters = computed(() => debouncedSearch.value.trim() !== '' || categorias.value.length > 0);

const toggleSelection = (id: number) => {
    selectedIds.value = selectedIds.value.includes(id) ? selectedIds.value.filter((selectedId) => selectedId !== id) : [...selectedIds.value, id];
};

const formatCurrency = (value: string | number) =>
    new Intl.NumberFormat('es-MX', {
        style: 'currency',
        currency: 'MXN',
    }).format(Number(value) || 0);

const addConcepts = async () => {
    if (!props.presupuestoId || selectedIds.value.length === 0) return;
    saving.value = true;

    try {
        const response = await axios.post(route('presupuesto.conceptos.agregar', props.presupuestoId), { conceptos: selectedIds.value });
        MyBasicToast.success(response.data.message);
        emit('added');
        emit('close');
    } catch (error) {
        if (axios.isAxiosError(error) && error.response?.status === 422) {
            MyBasicToast.error(error.response.data.message || 'Los conceptos seleccionados no están disponibles');
        } else {
            MyBasicToast.error('No fue posible agregar los conceptos');
        }
    } finally {
        saving.value = false;
    }
};

const buttonConfirm = computed<buttonconfirmed>(() => ({
    text: saving.value ? 'Agregando...' : `Agregar (${selectedIds.value.length})`,
    classname: 'bg-blue-700 text-white',
    disabled: saving.value || selectedIds.value.length === 0,
    onClick: addConcepts,
}));

watch(
    () => props.show,
    (show) => {
        if (!show) return;
        currentPage.value = 1;
        search.value = '';
        categorias.value = [];
        selectedIds.value = [];
        refreshKey.value++;
    },
);
</script>

<template>
    <BaseModal
        :show="show"
        modaltitle="Agregar conceptos"
        modaldescription="Selecciona conceptos disponibles para el presupuesto"
        position="center"
        z="z-[999]"
        :loading="saving"
        :buttonconfirm="buttonConfirm"
        @close="emit('close')"
    >
        <div class="flex min-h-[30rem] w-[min(70rem,calc(100vw-3rem))] flex-col">
            <div class="flex w-full flex-wrap items-end gap-2">
                <Search v-model="search" Classdiv="w-full sm:w-[28rem]" placeholder="Buscar por descripción del concepto" />
                <div class="w-full sm:w-[18rem]">
                    <MultiOptionFilter
                        v-if="presupuestoId"
                        v-model:selectedIds="categorias"
                        api="select2.presupuesto.categorias-conceptos"
                        :params="{ presupuesto_id: presupuestoId }"
                        label="Categoría"
                    />
                </div>
            </div>

            <Pagination
                v-if="presupuestoId"
                :key="refreshKey"
                api="presupuesto.conceptos.disponibles"
                :params="params"
                v-model:currentPage="currentPage"
                v-model:itemsPerPage="itemsPerPage"
                v-model:totalItems="totalItems"
                v-model:items="items"
                v-model:loading="loading"
                changesItems
            />

            <Table
                v-if="items.length > 0"
                classname="tabla mt-2"
                :titles="['', 'Descripción', 'Categoría', 'Precio', 'Aplicación', 'Vehículo de referencia']"
                :rows="
                    items.map((row) => ({
                        columns: [
                            {
                                element: SelectionCheckbox,
                                props: {
                                    id: row.id,
                                    checked: selectedIds.includes(row.id),
                                    onToggle: toggleSelection,
                                },
                            },
                            { element: escapeHtml(row.descripcion), classname: 'normal-case' },
                            { element: escapeHtml(row.categoria), classname: 'normal-case' },
                            { element: formatCurrency(row.total), classname: 'whitespace-nowrap text-right' },
                            { element: row.fijo ? 'Precio fijo' : 'Precio ajustable', classname: 'normal-case' },
                            { element: escapeHtml(row.vehiculo), classname: 'normal-case' },
                        ],
                    }))
                "
            />

            <div v-else-if="!loading" class="flex flex-1 items-center justify-center py-12 text-center text-gray-500">
                <span>
                    {{ hasFilters ? 'No se encontraron conceptos con los filtros seleccionados' : 'No hay conceptos disponibles para agregar' }}
                </span>
            </div>
        </div>
    </BaseModal>
</template>
