<script setup lang="ts">
import ZDRemoteSelect from '@/components/Zcrat/Elements/ZDRemoteSelect.vue';
import BaseModal from '@/components/Zcrat/modals/BaseModal.vue';
import type { option } from '@/types/generales';
import type { ConfirmButton } from '@/types/modals';
import MyBasicToast from '@/utils/ToastNotificationBasic';
import axios from 'axios';
import { computed, ref, watch } from 'vue';

const props = defineProps<{
    show: boolean;
    ordenServicioId: number | null;
    tallerActual: option | null;
}>();

const emit = defineEmits<{
    (event: 'close'): void;
    (event: 'saved'): void;
}>();

const saving = ref(false);
const selectedWorkshop = ref<option | null>(null);
const errors = ref<Record<string, string[]>>({});

watch(
    () => props.show,
    (show) => {
        if (!show) return;
        selectedWorkshop.value = null;
        errors.value = {};
    },
);

const updateWorkshop = async () => {
    if (props.ordenServicioId === null || selectedWorkshop.value === null) {
        return;
    }

    saving.value = true;
    errors.value = {};

    try {
        const response = await axios.patch(
            route(
                'ordenes-servicio.taller.update',
                props.ordenServicioId,
            ),
            { taller_id: selectedWorkshop.value.value },
        );
        MyBasicToast.success(response.data.message);
        emit('saved');
        emit('close');
    } catch (error) {
        if (axios.isAxiosError(error) && error.response?.status === 422) {
            errors.value = error.response.data.errors ?? {};
            MyBasicToast.error(
                error.response.data.message ?? 'Revisa el taller seleccionado',
            );
        } else {
            MyBasicToast.error('No fue posible cambiar el taller');
        }
    } finally {
        saving.value = false;
    }
};

const confirmButton = computed<ConfirmButton>(() => ({
    text: 'Cambiar taller',
    className: 'bg-blue-700 text-white',
    disabled: saving.value || selectedWorkshop.value === null,
    onClick: updateWorkshop,
}));
</script>

<template>
    <BaseModal
        :show="show"
        :saving="saving"
        saving-message="Espera a que termine la operación"
        modal-title="Cambiar taller"
        modal-description="Asigna la orden a otro taller"
        position="center"
        z-index-class="z-[999]"
        :confirm-button="confirmButton"
        @close="emit('close')"
    >
        <div class="flex w-[min(32rem,calc(100vw-3rem))] flex-col gap-4 pb-4">
            <div class="rounded-md bg-gray-100 px-4 py-3">
                <span class="text-sm text-gray-600">Taller actual</span>
                <p class="font-semibold">
                    {{ tallerActual?.label || 'Sin taller' }}
                </p>
            </div>

            <ZDRemoteSelect
                v-if="ordenServicioId"
                v-model="selectedWorkshop"
                endpoint="select2.talleres"
                label="Nuevo taller"
                placeholder="Buscar taller"
                classDiv="w-full"
                :disabled="saving"
                :errors="errors.taller_id"
                :DeleteErrors="() => delete errors.taller_id"
            />
        </div>
    </BaseModal>
</template>
