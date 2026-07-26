<script setup lang="ts">
import ZDRemoteSelect from '@/components/Zcrat/Elements/ZDRemoteSelect.vue';
import BaseModal from '@/components/Zcrat/modals/BaseModal.vue';
import type { option } from '@/types/generales';
import type { buttonconfirmed } from '@/types/modals';
import MyBasicToast from '@/utils/ToastNotificationBasic';
import axios from 'axios';
import { computed, ref, watch } from 'vue';

const props = defineProps<{
    show: boolean;
    ordenServicioId: number | null;
    moduloActual: option | null;
}>();

const emit = defineEmits<{
    (event: 'close'): void;
    (event: 'saved'): void;
}>();

const loading = ref(false);
const selectedModule = ref<option | null>(null);
const errors = ref<Record<string, string[]>>({});

watch(
    () => props.show,
    (show) => {
        if (!show) return;
        selectedModule.value = null;
        errors.value = {};
    },
);

const updateModule = async () => {
    if (props.ordenServicioId === null || selectedModule.value === null) return;

    loading.value = true;
    errors.value = {};

    try {
        const response = await axios.patch(
            route('ordenes-servicio.modulo.update', props.ordenServicioId),
            { modulo_id: selectedModule.value.value },
        );
        MyBasicToast.success(response.data.message);
        emit('saved');
        emit('close');
    } catch (error) {
        if (axios.isAxiosError(error) && error.response?.status === 422) {
            errors.value = error.response.data.errors ?? {};
            MyBasicToast.error(error.response.data.message ?? 'Revisa el módulo seleccionado');
        } else {
            MyBasicToast.error('No fue posible cambiar el módulo');
        }
    } finally {
        loading.value = false;
    }
};

const buttonConfirm = computed<buttonconfirmed>(() => ({
    text: 'Cambiar módulo',
    classname: 'bg-blue-700 text-white',
    disabled: loading.value || selectedModule.value === null,
    onClick: updateModule,
}));
</script>

<template>
    <BaseModal
        :show="show"
        :loading="loading"
        textLoading="Espera a que termine la operación"
        modaltitle="Cambiar módulo"
        modaldescription="La orden completa y todos sus presupuestos serán reasignados"
        position="center"
        :buttonconfirm="buttonConfirm"
        @close="emit('close')"
    >
        <div class="flex w-[min(32rem,calc(100vw-3rem))] flex-col gap-4 pb-4">
            <div class="rounded-md border border-amber-400 bg-amber-50 px-4 py-3 text-amber-900">
                <p class="font-semibold">Este cambio afecta a toda la orden.</p>
                <p class="text-sm">
                    Se conservarán los conceptos históricos, pero los nuevos conceptos disponibles corresponderán al módulo nuevo.
                </p>
            </div>

            <div class="rounded-md bg-gray-100 px-4 py-3">
                <span class="text-sm text-gray-600">Módulo actual</span>
                <p class="font-semibold">{{ moduloActual?.label || 'Sin módulo' }}</p>
            </div>

            <ZDRemoteSelect
                v-if="ordenServicioId"
                v-model="selectedModule"
                endpoint="select2.ordenes-servicio.modulos-cambio"
                :params="{ orden_servicio_id: ordenServicioId }"
                label="Nuevo módulo"
                placeholder="Buscar módulo"
                classDiv="w-full"
                :disabled="loading"
                :errors="errors.modulo_id"
                :DeleteErrors="() => delete errors.modulo_id"
            />
        </div>
    </BaseModal>
</template>
