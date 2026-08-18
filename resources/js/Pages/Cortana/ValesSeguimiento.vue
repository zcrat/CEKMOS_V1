<script setup lang="ts">
import Pagination from '@/components/Zcrat/Filters/pagination.vue';
import Search from '@/components/Zcrat/Inputs/Search.vue';
import EntregaConceptosValeModal from '@/components/Zcrat/modals/EntregaConceptosValeModal.vue';
import { useAuth } from '@/composables/useAuth';
import AppLayout from '@/layouts/AppLayout.vue';
import { Link } from '@inertiajs/vue3';
import { useDebounce } from '@vueuse/core';
import { computed, ref } from 'vue';

type ValeTipo = 'almacen' | 'subcontratos';

interface ValeSeguimiento {
    id: number;
    orden: string;
    folio: string;
    creacion: string;
    estatus: string;
    destino: string;
    entregado?: boolean;
    confirmado?: boolean;
    fecha_termino?: string | null;
    refacciones_entregadas?: number;
    refacciones_total?: number;
    enviado?: boolean;
    autorizado?: boolean;
}

const props = defineProps<{
    tipo: ValeTipo;
}>();

const { can } = useAuth();
const canDeliver = can('editar.vales.almacen');

const currentPage = ref(1);
const itemsPerPage = ref(10);
const totalItems = ref(0);
const items = ref<ValeSeguimiento[]>([]);
const loading = ref(true);
const search = ref('');
const refresh = ref(0);
const deliveryVale = ref<ValeSeguimiento | null>(null);
const debouncedSearch = useDebounce(search, 400);

const isWarehouse = computed(() => props.tipo === 'almacen');
const title = computed(() => (isWarehouse.value ? 'Vales de almacén' : 'Vales de subcontratos'));
const otherTipo = computed<ValeTipo>(() => (isWarehouse.value ? 'subcontratos' : 'almacen'));
const otherLabel = computed(() => (isWarehouse.value ? 'Ver vales de subcontratos' : 'Ver vales de almacén'));
const canOpenOther = computed(() => can(`ver.vales.${otherTipo.value}`));
const api = 'vales.seguimiento.index';
const params = computed(() => ({ tipo: props.tipo, search: debouncedSearch.value, refresh: refresh.value }));

const statusClass = (active: boolean) =>
    active ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800';

const refreshItems = () => {
    refresh.value++;
};
</script>

<template>
    <AppLayout :title="title" :loading="loading" loading-message="Cargando vales">
        <template #header>
            <Link
                v-if="canOpenOther"
                :href="route('vales.seguimiento.view', { tipo: otherTipo })"
                class="rounded-md bg-[#176cb3] px-4 py-2 font-semibold text-white transition hover:bg-blue-800"
            >
                {{ otherLabel }}
            </Link>
        </template>

        <template #filtering>
            <Search v-model="search" Classdiv="w-full sm:w-[30rem]" placeholder="Buscar por orden, folio o destino" />
            <Pagination
                :key="tipo"
                :api="api"
                :params="params"
                v-model:currentPage="currentPage"
                v-model:itemsPerPage="itemsPerPage"
                v-model:totalItems="totalItems"
                v-model:items="items"
                v-model:loading="loading"
                changesItems
            />
        </template>

        <template #content>
            <div v-if="items.length" class="overflow-x-auto rounded-md border bg-white">
                <table class="w-full min-w-[900px] text-sm">
                    <thead class="bg-gray-100 text-left uppercase">
                        <tr>
                            <th class="px-3 py-2">Orden servicio</th>
                            <th class="px-3 py-2">Folio</th>
                            <th class="px-3 py-2">Creación</th>
                            <template v-if="isWarehouse">
                                <th class="px-3 py-2">Entregado</th>
                                <th class="px-3 py-2">Confirmado</th>
                                <th class="px-3 py-2">Fecha término</th>
                                <th class="px-3 py-2">Refacciones</th>
                                <th class="px-3 py-2">Opciones</th>
                            </template>
                            <template v-else>
                                <th class="px-3 py-2">Destino</th>
                                <th class="px-3 py-2">Enviado</th>
                                <th class="px-3 py-2">Autorizado</th>
                            </template>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="vale in items" :key="vale.id" class="border-t hover:bg-gray-50">
                            <td class="px-3 py-2 font-semibold">{{ vale.orden }}</td>
                            <td class="px-3 py-2">{{ vale.folio }}</td>
                            <td class="whitespace-nowrap px-3 py-2">{{ vale.creacion }}</td>
                            <template v-if="isWarehouse">
                                <td class="px-3 py-2">
                                    <span :class="['rounded-full px-2 py-1 text-xs font-semibold', statusClass(Boolean(vale.entregado))]">
                                        {{ vale.entregado ? 'Entregado' : 'Pendiente' }}
                                    </span>
                                </td>
                                <td class="px-3 py-2">
                                    <span :class="['rounded-full px-2 py-1 text-xs font-semibold', statusClass(Boolean(vale.confirmado))]">
                                        {{ vale.confirmado ? 'Confirmado' : 'Pendiente' }}
                                    </span>
                                </td>
                                <td class="whitespace-nowrap px-3 py-2">{{ vale.fecha_termino || '-' }}</td>
                                <td class="px-3 py-2">
                                    <span class="rounded-full bg-blue-100 px-3 py-1 text-xs font-semibold text-blue-800">
                                        {{ vale.refacciones_entregadas ?? 0 }} / {{ vale.refacciones_total ?? 0 }}
                                    </span>
                                </td>
                                <td class="px-3 py-2">
                                    <button
                                        v-if="canDeliver"
                                        type="button"
                                        class="rounded-md bg-emerald-700 px-3 py-2 text-xs font-semibold text-white disabled:opacity-50"
                                        :disabled="vale.estatus === 'Sin Entregar'"
                                        @click="deliveryVale = vale"
                                    >
                                        Registrar entrega
                                    </button>
                                    <span v-else>-</span>
                                </td>
                            </template>
                            <template v-else>
                                <td class="px-3 py-2">{{ vale.destino }}</td>
                                <td class="px-3 py-2">
                                    <span :class="['rounded-full px-2 py-1 text-xs font-semibold', statusClass(Boolean(vale.enviado))]">
                                        {{ vale.enviado ? 'Enviado' : 'Pendiente' }}
                                    </span>
                                </td>
                                <td class="px-3 py-2">
                                    <span :class="['rounded-full px-2 py-1 text-xs font-semibold', statusClass(Boolean(vale.autorizado))]">
                                        {{ vale.autorizado ? 'Autorizado' : 'Pendiente' }}
                                    </span>
                                </td>
                            </template>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div v-else-if="!loading" class="flex flex-1 items-center justify-center py-12 text-lg">
                No hay vales para mostrar.
            </div>
        </template>
    </AppLayout>

    <EntregaConceptosValeModal
        :show="deliveryVale !== null"
        :vale="deliveryVale"
        @close="deliveryVale = null"
        @saved="refreshItems"
    />
</template>
