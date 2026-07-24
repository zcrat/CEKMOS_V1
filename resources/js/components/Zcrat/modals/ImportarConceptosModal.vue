<script setup lang="ts">
import BaseModal from '@/components/Zcrat/modals/BaseModal.vue';
import type { buttonconfirmed } from '@/types/modals';
import MyBasicToast from '@/utils/ToastNotificationBasic';
import axios from 'axios';
import { computed, ref, watch } from 'vue';

const props = defineProps<{
    show: boolean;
}>();

const emit = defineEmits<{
    (event: 'close'): void;
    (event: 'queued'): void;
}>();

const fileInput = ref<HTMLInputElement | null>(null);
const selectedFile = ref<File | null>(null);
const importing = ref(false);
const dragging = ref(false);

const formattedSize = computed(() => {
    if (!selectedFile.value) return '';

    return `${(selectedFile.value.size / 1024 / 1024).toFixed(2)} MB`;
});

const reset = () => {
    selectedFile.value = null;
    dragging.value = false;
    if (fileInput.value) fileInput.value.value = '';
};

watch(
    () => props.show,
    (show) => {
        if (show) reset();
    },
);

const setFile = (file?: File) => {
    if (!file) return;

    if (!/\.(xlsx|xls)$/i.test(file.name)) {
        MyBasicToast.error('Selecciona un archivo Excel con extensión .xlsx o .xls');
        reset();

        return;
    }

    selectedFile.value = file;
};

const chooseFile = () => {
    if (!importing.value) fileInput.value?.click();
};

const handleInput = (event: Event) => {
    const input = event.target as HTMLInputElement;
    setFile(input.files?.[0]);
};

const handleDrop = (event: DragEvent) => {
    dragging.value = false;
    setFile(event.dataTransfer?.files?.[0]);
};

const downloadTemplate = () => {
    window.location.href = route('catalogos.conceptos.plantilla');
};

const importFile = async () => {
    if (!selectedFile.value) return;

    importing.value = true;
    const formData = new FormData();
    formData.append('archivo', selectedFile.value);

    try {
        const response = await axios.post(route('catalogos.conceptos.importar'), formData, {
            headers: { 'Content-Type': 'multipart/form-data' },
        });
        MyBasicToast.success(response.data.message);
        emit('queued');
        emit('close');
    } catch (error) {
        if (axios.isAxiosError(error) && error.response?.status === 422) {
            const messages = Object.values(error.response.data.errors ?? {})
                .flat()
                .slice(0, 3)
                .join(' ');
            MyBasicToast.error(messages || 'El archivo contiene datos inválidos');
        } else {
            MyBasicToast.error('No fue posible enviar el archivo a procesamiento');
        }
    } finally {
        importing.value = false;
    }
};

const buttonConfirm = computed<buttonconfirmed>(() => ({
    text: importing.value ? 'Enviando...' : 'Procesar archivo',
    classname: 'bg-green-700 text-white',
    disabled: importing.value || selectedFile.value === null,
    onClick: importFile,
}));
</script>

<template>
    <BaseModal
        :show="show"
        :loading="importing"
        textLoading="Espera a que el archivo sea enviado"
        modaltitle="Importar conceptos de contratos"
        modaldescription="Selecciona o arrastra un archivo Excel para procesarlo"
        position="center"
        :buttonconfirm="buttonConfirm"
        @close="emit('close')"
    >
        <div class="w-[min(90vw,38rem)] py-3">
            <input ref="fileInput" class="hidden" type="file" accept=".xlsx,.xls" @change="handleInput" />

            <button
                type="button"
                :class="[
                    'flex min-h-56 w-full flex-col items-center justify-center gap-3 rounded-xl border-2 border-dashed px-6 py-8 text-center transition-colors',
                    dragging ? 'border-blue-600 bg-blue-50' : 'border-gray-400 bg-gray-50 hover:border-blue-500 hover:bg-blue-50',
                ]"
                :disabled="importing"
                @click="chooseFile"
                @dragenter.prevent="dragging = true"
                @dragover.prevent="dragging = true"
                @dragleave.prevent="dragging = false"
                @drop.prevent="handleDrop"
            >
                <font-awesome-icon :icon="selectedFile ? 'fa-solid fa-file-excel' : 'fa-solid fa-cloud-arrow-up'" class="text-5xl text-green-700" />

                <template v-if="selectedFile">
                    <span class="max-w-full break-all text-lg font-semibold">{{ selectedFile.name }}</span>
                    <span class="text-sm text-gray-600">{{ formattedSize }}</span>
                    <span class="text-sm text-blue-700">Haz clic o arrastra otro archivo para reemplazarlo</span>
                </template>
                <template v-else>
                    <span class="text-lg font-semibold">Arrastra aquí tu archivo Excel</span>
                    <span class="text-sm text-gray-600">o haz clic para seleccionarlo</span>
                    <span class="text-xs text-gray-500">Formatos permitidos: .xlsx y .xls · Máximo 10 MB</span>
                </template>
            </button>
        </div>

        <template #footer>
            <button
                type="button"
                class="flex h-10 items-center gap-2 rounded-md bg-gray-600 px-4 py-2 text-white"
                :disabled="importing"
                @click="downloadTemplate"
            >
                <font-awesome-icon icon="fa-solid fa-file-arrow-down" />
                Descargar plantilla
            </button>
        </template>
    </BaseModal>
</template>
