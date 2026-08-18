<script setup lang="ts">
import BaseModal from '@/components/Zcrat/modals/BaseModal.vue';
import type { ConfirmButton } from '@/types/modals';
import MyBasicToast from '@/utils/ToastNotificationBasic';
import axios from 'axios';
import { computed, ref, watch } from 'vue';

interface ValeEntrega {
    id: number;
    folio: string;
    orden: string;
}

interface ConceptoEntrega {
    id: number;
    cantidad: number;
    clave: string;
    concepto: string;
    fecha_entrega: string | null;
    seleccionado: boolean;
    fecha: string;
}

const props = defineProps<{
    show: boolean;
    vale: ValeEntrega | null;
}>();

const emit = defineEmits<{
    (event: 'close'): void;
    (event: 'saved'): void;
}>();

const loading = ref(false);
const saving = ref(false);
const conceptos = ref<ConceptoEntrega[]>([]);
const errors = ref<Record<string, string[]>>({});

const nowForInput = () => {
    const date = new Date();
    date.setMinutes(date.getMinutes() - date.getTimezoneOffset());

    return date.toISOString().slice(0, 16);
};

const maxDate = ref(nowForInput());
const selectedCount = computed(() => conceptos.value.filter((concepto) => concepto.seleccionado).length);

const loadDeliveries = async () => {
    if (!props.vale) return;

    loading.value = true;
    errors.value = {};
    maxDate.value = nowForInput();

    try {
        const { data } = await axios.get(route('vales-almacen.entregas.index', props.vale.id));
        conceptos.value = data.conceptos.map((concepto: Omit<ConceptoEntrega, 'seleccionado' | 'fecha'>) => ({
            ...concepto,
            seleccionado: concepto.fecha_entrega !== null,
            fecha: concepto.fecha_entrega ?? nowForInput(),
        }));
    } catch (error) {
        MyBasicToast.error(
            axios.isAxiosError(error)
                ? (error.response?.data?.message ?? 'No fue posible cargar los conceptos del vale.')
                : 'No fue posible cargar los conceptos del vale.',
        );
        emit('close');
    } finally {
        loading.value = false;
    }
};

watch(
    () => props.show,
    (show) => {
        if (show) void loadDeliveries();
    },
);

const save = async () => {
    if (!props.vale || selectedCount.value === 0) {
        MyBasicToast.warning('Selecciona al menos un concepto.');
        return;
    }

    saving.value = true;
    errors.value = {};

    try {
        const { data } = await axios.patch(route('vales-almacen.entregas.store', props.vale.id), {
            entregas: conceptos.value
                .filter((concepto) => concepto.seleccionado)
                .map((concepto) => ({
                    id: concepto.id,
                    fecha_entrega: concepto.fecha,
                })),
        });
        MyBasicToast.success(data.message);
        emit('saved');
        emit('close');
    } catch (error) {
        if (axios.isAxiosError(error) && error.response?.status === 422) {
            errors.value = error.response.data.errors ?? {};
            MyBasicToast.warning(error.response.data.message ?? 'Revisa las fechas de entrega.');
        } else {
            MyBasicToast.error(
                axios.isAxiosError(error)
                    ? (error.response?.data?.message ?? 'No fue posible registrar las entregas.')
                    : 'No fue posible registrar las entregas.',
            );
        }
    } finally {
        saving.value = false;
    }
};

const confirmButton = computed<ConfirmButton>(() => ({
    text: `Guardar entregas (${selectedCount.value})`,
    className: 'bg-emerald-700 text-white',
    disabled: loading.value || saving.value || selectedCount.value === 0,
    onClick: save,
}));
</script>

<template>
    <BaseModal
        :show="show"
        :loading="loading"
        :saving="saving"
        :modal-title="`Entrega de refacciones · ${vale?.folio ?? ''}`"
        :modal-description="`Entrega múltiple de conceptos del vale ${vale?.folio ?? ''}`"
        loading-message="Cargando conceptos"
        saving-message="Guardando entregas"
        modal-class="w-[min(70rem,calc(100vw-1rem))]"
        :confirm-button="confirmButton"
        @close="emit('close')"
    >
        <div class="mb-3 rounded-md bg-blue-50 px-4 py-3 text-sm text-blue-900">
            Orden de servicio: <strong>{{ vale?.orden || '-' }}</strong>
        </div>

        <div class="max-h-[60vh] overflow-auto rounded-md border">
            <table class="w-full min-w-[760px] text-sm">
                <thead class="sticky top-0 bg-gray-100 text-left">
                    <tr>
                        <th class="px-3 py-2">Cantidad</th>
                        <th class="px-3 py-2">Concepto</th>
                        <th class="px-3 py-2">Clave</th>
                        <th class="px-3 py-2">Opciones</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(concepto, index) in conceptos" :key="concepto.id" class="border-t">
                        <td class="px-3 py-2 text-center">{{ concepto.cantidad }}</td>
                        <td class="px-3 py-2">{{ concepto.concepto }}</td>
                        <td class="px-3 py-2">{{ concepto.clave || '-' }}</td>
                        <td class="px-3 py-2">
                            <div class="flex items-center gap-3">
                                <label class="flex items-center gap-2 whitespace-nowrap font-medium">
                                    <input v-model="concepto.seleccionado" type="checkbox" class="rounded border-gray-300" />
                                    Entregado
                                </label>
                                <input
                                    v-model="concepto.fecha"
                                    type="datetime-local"
                                    :max="maxDate"
                                    :disabled="!concepto.seleccionado"
                                    class="rounded-md border-gray-300 disabled:bg-gray-100"
                                />
                            </div>
                            <span v-if="errors[`entregas.${index}.fecha_entrega`]?.[0]" class="mt-1 block text-xs text-red-600">
                                {{ errors[`entregas.${index}.fecha_entrega`][0] }}
                            </span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </BaseModal>
</template>
