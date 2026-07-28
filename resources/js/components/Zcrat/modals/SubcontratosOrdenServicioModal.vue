<script setup lang="ts">
import BaseModal from '@/components/Zcrat/modals/BaseModal.vue';
import MyBasicToast from '@/utils/ToastNotificationBasic';
import axios from 'axios';
import { computed, ref, watch } from 'vue';

interface SubcontractHistoryItem {
    id: number;
    fecha_inicio: string;
    fecha_fin: string | null;
    usuario: string;
    activo: boolean;
}

const props = defineProps<{
    show: boolean;
    ordenServicioId: number | null;
    orden: string;
}>();

const emit = defineEmits<{
    (event: 'close'): void;
    (event: 'saved'): void;
}>();

const loading = ref(false);
const saving = ref(false);
const history = ref<SubcontractHistoryItem[]>([]);
const hasActive = computed(() => history.value.some((item) => item.activo));

const formatDate = (value: string | null) => {
    if (!value) return 'Activo';

    return new Intl.DateTimeFormat('es-MX', {
        dateStyle: 'medium',
        timeStyle: 'short',
    }).format(new Date(value));
};

const loadHistory = async () => {
    if (props.ordenServicioId === null) return;

    loading.value = true;
    try {
        const response = await axios.get(
            route(
                'ordenes-servicio.subcontratos.index',
                props.ordenServicioId,
            ),
        );
        history.value = response.data.subcontratos ?? [];
    } catch {
        MyBasicToast.error('No fue posible obtener los subcontratos');
        emit('close');
    } finally {
        loading.value = false;
    }
};

const startSubcontract = async () => {
    if (props.ordenServicioId === null || hasActive.value) return;

    saving.value = true;
    try {
        const response = await axios.post(
            route(
                'ordenes-servicio.subcontratos.store',
                props.ordenServicioId,
            ),
        );
        MyBasicToast.success(response.data.message);
        await loadHistory();
        emit('saved');
    } catch (error) {
        const message = axios.isAxiosError(error)
            ? error.response?.data?.errors?.subcontrato?.[0]
                ?? error.response?.data?.message
            : null;
        MyBasicToast.error(message ?? 'No fue posible iniciar el subcontrato');
    } finally {
        saving.value = false;
    }
};

const finishSubcontract = async (subcontractId: number) => {
    saving.value = true;
    try {
        const response = await axios.patch(
            route('subcontratos.finalizar', subcontractId),
        );
        MyBasicToast.success(response.data.message);
        await loadHistory();
        emit('saved');
    } catch (error) {
        const message = axios.isAxiosError(error)
            ? error.response?.data?.errors?.subcontrato?.[0]
                ?? error.response?.data?.message
            : null;
        MyBasicToast.error(message ?? 'No fue posible finalizar el subcontrato');
    } finally {
        saving.value = false;
    }
};

watch(
    () => props.show,
    (show) => {
        if (show) {
            history.value = [];
            loadHistory();
        }
    },
);
</script>

<template>
    <BaseModal
        :show="show"
        :loading="loading"
        :saving="saving"
        loading-message="Cargando historial"
        saving-message="Espera a que termine la operación"
        modal-title="Subcontratos"
        :modal-description="`Historial de subcontratos de la orden ${orden}`"
        position="center"
        z-index-class="z-[999]"
        @close="emit('close')"
    >
        <div class="flex w-[min(48rem,calc(100vw-3rem))] flex-col gap-4 pb-4">
            <div class="flex items-center justify-between gap-4">
                <div>
                    <p class="text-sm text-gray-600">Orden</p>
                    <p class="font-semibold">{{ orden }}</p>
                </div>
                <button
                    v-if="!hasActive"
                    class="rounded-md bg-blue-700 px-4 py-2 text-white disabled:opacity-50"
                    :disabled="saving"
                    @click="startSubcontract"
                >
                    Ingresar a subcontrato
                </button>
                <span
                    v-else
                    class="rounded-full bg-amber-100 px-3 py-1 text-sm font-medium text-amber-800"
                >
                    Subcontrato activo
                </span>
            </div>

            <div class="overflow-x-auto rounded-md border border-gray-300">
                <table class="w-full text-left text-sm">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-3 py-2">Inicio</th>
                            <th class="px-3 py-2">Fin</th>
                            <th class="px-3 py-2">Registró</th>
                            <th class="px-3 py-2 text-right">Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr
                            v-for="item in history"
                            :key="item.id"
                            class="border-t border-gray-200"
                        >
                            <td class="whitespace-nowrap px-3 py-2">
                                {{ formatDate(item.fecha_inicio) }}
                            </td>
                            <td class="whitespace-nowrap px-3 py-2">
                                {{ formatDate(item.fecha_fin) }}
                            </td>
                            <td class="px-3 py-2">{{ item.usuario }}</td>
                            <td class="px-3 py-2 text-right">
                                <button
                                    v-if="item.activo"
                                    class="rounded-md bg-red-700 px-3 py-1 text-white disabled:opacity-50"
                                    :disabled="saving"
                                    @click="finishSubcontract(item.id)"
                                >
                                    Finalizar
                                </button>
                                <span v-else class="text-gray-500">Finalizado</span>
                            </td>
                        </tr>
                        <tr v-if="history.length === 0">
                            <td colspan="4" class="px-3 py-8 text-center text-gray-500">
                                La orden no tiene subcontratos.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </BaseModal>
</template>
