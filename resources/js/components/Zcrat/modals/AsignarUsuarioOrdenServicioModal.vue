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
    usuarioActual: option | null;
}>();

const emit = defineEmits<{
    (event: 'close'): void;
    (event: 'saved'): void;
}>();

const saving = ref(false);
const selectedUser = ref<option | null>(null);
const errors = ref<Record<string, string[]>>({});

watch(
    () => props.show,
    (show) => {
        if (!show) return;
        selectedUser.value = null;
        errors.value = {};
    },
);

const assignUser = async () => {
    if (props.ordenServicioId === null || selectedUser.value === null) return;

    saving.value = true;
    errors.value = {};

    try {
        const response = await axios.patch(
            route(
                'ordenes-servicio.usuario-asignado.update',
                props.ordenServicioId,
            ),
            { user_id: selectedUser.value.value },
        );
        MyBasicToast.success(response.data.message);
        emit('saved');
        emit('close');
    } catch (error) {
        if (axios.isAxiosError(error) && error.response?.status === 422) {
            errors.value = error.response.data.errors ?? {};
            MyBasicToast.error(
                errors.value.user_id?.[0]
                    ?? error.response.data.message
                    ?? 'Revisa el usuario seleccionado',
            );
        } else {
            MyBasicToast.error('No fue posible asignar el usuario');
        }
    } finally {
        saving.value = false;
    }
};

const confirmButton = computed<ConfirmButton>(() => ({
    text: props.usuarioActual ? 'Cambiar usuario' : 'Asignar usuario',
    className: 'bg-blue-700 text-white',
    disabled: saving.value || selectedUser.value === null,
    onClick: assignUser,
}));
</script>

<template>
    <BaseModal
        :show="show"
        :saving="saving"
        saving-message="Espera a que termine la operación"
        :modal-title="usuarioActual ? 'Cambiar usuario asignado' : 'Asignar usuario'"
        modal-description="Asigna un usuario del taller actual a la orden"
        position="center"
        :confirm-button="confirmButton"
        @close="emit('close')"
    >
        <div class="flex w-[min(32rem,calc(100vw-3rem))] flex-col gap-4 pb-4">
            <div class="rounded-md bg-gray-100 px-4 py-3">
                <span class="text-sm text-gray-600">Usuario actual</span>
                <p class="font-semibold">
                    {{ usuarioActual?.label || 'Sin asignar' }}
                </p>
            </div>

            <ZDRemoteSelect
                v-if="ordenServicioId"
                v-model="selectedUser"
                endpoint="ordenes-servicio.usuarios-asignables"
                :params="{ orden_servicio_id: ordenServicioId }"
                label="Usuario"
                placeholder="Buscar usuario"
                classDiv="w-full z-[50]"
                :disabled="saving"
                :errors="errors.user_id"
                :DeleteErrors="() => delete errors.user_id"
            />
        </div>
    </BaseModal>
</template>
