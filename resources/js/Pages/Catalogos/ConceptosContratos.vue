<script setup lang="ts">
import HistorialImportacionesConceptos from '@/components/Zcrat/Conceptos/HistorialImportacionesConceptos.vue';
import Button from '@/components/Zcrat/Inputs/Button.vue';
import ImportarConceptosModal from '@/components/Zcrat/modals/ImportarConceptosModal.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { ref } from 'vue';

const showImportModal = ref(false);
const history = ref<{ refresh: () => Promise<void> } | null>(null);

const refreshHistory = () => {
    history.value?.refresh();
};
</script>

<template>
    <AppLayout
        title="Conceptos Contratos"
        description="Importación e historial de conceptos proporcionados por contratos"
        :loading="false"
    >
        <template #header>
            <Button text="Importar archivo" type="save" icon="fa-solid fa-file-import" @click="showImportModal = true" />
        </template>

        <template #content>
            <div class="flex min-h-0 flex-1 flex-col py-2">
                <HistorialImportacionesConceptos ref="history" />
            </div>
        </template>
    </AppLayout>

    <ImportarConceptosModal
        :show="showImportModal"
        @close="showImportModal = false"
        @queued="refreshHistory"
    />
</template>
