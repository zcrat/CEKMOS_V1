<script setup lang="ts">
import ZDRemoteSelect from '@/components/Zcrat/Elements/ZDRemoteSelect.vue';
import InputBasic from '@/components/Zcrat/Inputs/form/InputBasic.vue';
import BaseModal from '@/components/Zcrat/modals/BaseModal.vue';
import type { option } from '@/types/generales';
import type { ConfirmButton } from '@/types/modals';
import MyBasicToast from '@/utils/ToastNotificationBasic';
import axios from 'axios';
import { computed, ref, watch } from 'vue';

const props = defineProps<{
    show: boolean;
    ordenServicioId: number | null;
    orden: string;
    datosEntrada: {
        fecha: string | null;
        kilometraje: number | null;
        gasolina: string | null;
    } | null;
}>();

const emit = defineEmits<{
    (event: 'close'): void;
    (event: 'saved'): void;
}>();

const kilometraje = ref<number | null>(null);
const gasolina = ref<option | null>(null);
const saving = ref(false);
const errors = ref<Record<string, string[]>>({});

const dateFormatter = new Intl.DateTimeFormat('es-MX', {
    dateStyle: 'medium',
    timeStyle: 'short',
});

const fechaEntrada = computed(() => {
    if (!props.datosEntrada?.fecha) return '-';

    const date = new Date(props.datosEntrada.fecha);

    return Number.isNaN(date.getTime()) ? '-' : dateFormatter.format(date);
});

watch(
    () => props.show,
    (show) => {
        if (!show) return;

        kilometraje.value = null;
        gasolina.value = null;
        errors.value = {};
    },
);

const saveDelivery = async () => {
    if (props.ordenServicioId === null || kilometraje.value === null || gasolina.value === null) {
        return;
    }

    saving.value = true;
    errors.value = {};

    try {
        const response = await axios.patch(route('ordenes-servicio.seguimiento.update', props.ordenServicioId), {
            accion: 'entregar_unidad',
            kilometraje: kilometraje.value,
            gasolina: gasolina.value.value,
        });
        MyBasicToast.success(response.data.message);
        emit('saved');
        emit('close');
    } catch (error) {
        if (axios.isAxiosError(error) && error.response?.status === 422) {
            errors.value = error.response.data.errors ?? {};
            MyBasicToast.error(error.response.data.message ?? 'Revisa los datos de salida capturados');
        } else {
            MyBasicToast.error('No fue posible entregar la unidad');
        }
    } finally {
        saving.value = false;
    }
};

const confirmButton = computed<ConfirmButton>(() => ({
    text: 'Entregar unidad',
    className: 'bg-emerald-700 text-white',
    disabled: saving.value || kilometraje.value === null || gasolina.value === null,
    onClick: saveDelivery,
}));
</script>

<template>
    <BaseModal
        :show="show"
        :saving="saving"
        saving-message="Espera a que termine la operación"
        modal-title="Entregar unidad"
        :modal-description="`Captura los datos de salida de la orden ${orden}`"
        position="center"
        :confirm-button="confirmButton"
        @close="emit('close')"
    >
        <div class="grid w-[min(34rem,calc(100vw-3rem))] grid-cols-1 gap-4 pb-4 sm:grid-cols-2">
            <div class="rounded-md bg-gray-100 px-4 py-3 sm:col-span-2">
                <span class="text-sm text-gray-600">Orden de servicio</span>
                <p class="font-semibold">{{ orden }}</p>
            </div>

            <div class="grid grid-cols-1 gap-3 rounded-md border border-blue-200 bg-blue-50 px-4 py-3 sm:col-span-2 sm:grid-cols-3">
                <div>
                    <span class="text-xs text-gray-600">Entrada</span>
                    <p class="font-semibold">{{ fechaEntrada }}</p>
                </div>
                <div>
                    <span class="text-xs text-gray-600"> Kilometraje de entrada </span>
                    <p class="font-semibold">
                        {{ datosEntrada?.kilometraje ?? '-' }}
                    </p>
                </div>
                <div>
                    <span class="text-xs text-gray-600"> Gasolina de entrada </span>
                    <p class="font-semibold">
                        {{ datosEntrada?.gasolina ?? '-' }}
                    </p>
                </div>
            </div>

            <InputBasic
                id="kilometraje-salida"
                v-model="kilometraje"
                label="Kilometraje"
                type="number"
                placeholder="Ej. 125000"
                :disabled="saving"
                :errors="errors.kilometraje"
                :DeleteErrors="() => delete errors.kilometraje"
            />

            <ZDRemoteSelect
                v-model="gasolina"
                endpoint="select.niveles.combustible"
                label="Nivel de gasolina"
                placeholder="Seleccionar nivel"
                classDiv="w-full"
                :searchable="false"
                :clearable="false"
                :disabled="saving"
                :errors="errors.gasolina"
                :DeleteErrors="() => delete errors.gasolina"
            />
        </div>
    </BaseModal>
</template>
