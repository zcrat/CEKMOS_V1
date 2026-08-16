<script setup lang="ts">
import Loading from '@/components/Zcrat/Elements/Loading.vue';
import Button from '@/components/Zcrat/Inputs/Button.vue';
import ValeAlmacenForm from '@/components/Zcrat/Forms/ValeAlmacenForm.vue';
import { useAuth } from '@/composables/useAuth';
import type { RecepcionesVehiculares } from '@/types/generales';
import MyBasicToast from '@/utils/ToastNotificationBasic';
import { ZdAlert } from '@/utils/ZdAlert';
import axios from 'axios';
import { computed, ref } from 'vue';
import BaseModal from './BaseModal.vue';

interface OrdenValeAlmacen {
    id: number;
    numero: string;
    economico: string;
    placas: string;
    motor: string;
}

interface ConceptoValeAlmacen {
    clave: string;
    cantidad: number;
    descripcion: string;
}

interface ValeAlmacen {
    id: number;
    folio: string;
    destino: string;
    motor: string;
    fecha: string;
    estatus: string;
    pdf_url: string;
    conceptos: ConceptoValeAlmacen[];
}

const { can } = useAuth();
const canCreate = can('crear.vales.almacen');
const canEdit = can('editar.vales.almacen');
const canDelete = can('eliminar.vales.almacen');

const show = ref(false);
const loading = ref(false);
const deleting = ref(false);
const formSaving = ref(false);
const iframeLoading = ref(false);
const iframeVersion = ref(0);
const idVisualizacion = ref<number | null>(null);
const editingVale = ref<ValeAlmacen | null>(null);
const orden = ref<OrdenValeAlmacen | null>(null);
const vales = ref<ValeAlmacen[]>([]);
const loadError = ref('');

const title = computed(() => `Vales de almacén para ${orden.value?.economico || orden.value?.numero || ''}`);
const selectedVale = computed(() => vales.value.find((vale) => vale.id === idVisualizacion.value) ?? null);
const canEditSelected = computed(() => canEdit && selectedVale.value?.estatus === 'Sin Entregar');
const selectedPdfUrl = computed(() => (selectedVale.value ? `${selectedVale.value.pdf_url}?v=${iframeVersion.value}` : ''));
const busy = computed(() => deleting.value || formSaving.value);

const selectVale = (vale: ValeAlmacen, forceReload = false) => {
    if (!forceReload && idVisualizacion.value === vale.id) return;

    editingVale.value = null;
    idVisualizacion.value = vale.id;
    iframeLoading.value = true;
    iframeVersion.value++;
};

const loadVales = async (ordenServicioId: number) => {
    loading.value = true;
    loadError.value = '';

    try {
        const { data } = await axios.get(route('vales-almacen.index', ordenServicioId));
        orden.value = data.orden;
        vales.value = data.vales;
        idVisualizacion.value = vales.value[0]?.id ?? null;
        iframeLoading.value = idVisualizacion.value !== null;
        iframeVersion.value++;
    } catch (error) {
        loadError.value = axios.isAxiosError(error)
            ? (error.response?.data?.message ?? 'No fue posible cargar los vales de almacén.')
            : 'No fue posible cargar los vales de almacén.';
        MyBasicToast.error(loadError.value);
    } finally {
        loading.value = false;
    }
};

const Open = (row: RecepcionesVehiculares) => {
    orden.value = {
        id: row.id,
        numero: row.orden,
        economico: row.economico,
        placas: row.placas,
        motor: '',
    };
    vales.value = [];
    idVisualizacion.value = null;
    editingVale.value = null;
    show.value = true;
    void loadVales(row.id);
};

const close = () => {
    if (busy.value) return;
    editingVale.value = null;
    show.value = false;
};

const openCreate = () => {
    if (formSaving.value) return;
    editingVale.value = null;
    idVisualizacion.value = null;
};

const openEdit = () => {
    if (!canEditSelected.value || !selectedVale.value) return;
    editingVale.value = selectedVale.value;
    idVisualizacion.value = null;
};

const cancelForm = () => {
    const vale = editingVale.value ?? vales.value[0] ?? null;
    editingVale.value = null;

    if (vale) {
        selectVale(vale, true);
    } else {
        close();
    }
};

const handleValeSaved = (vale: ValeAlmacen) => {
    const index = vales.value.findIndex((item) => item.id === vale.id);

    if (index === -1) {
        vales.value.unshift(vale);
    } else {
        vales.value.splice(index, 1, vale);
    }

    editingVale.value = null;
    selectVale(vale, true);
};

const deleteSelectedVale = async () => {
    if (!canDelete || !selectedVale.value) return;

    const vale = selectedVale.value;
    const confirmed = await ZdAlert({
        title: `Eliminar vale ${vale.folio}`,
        text: 'Esta acción eliminará el vale y todos sus conceptos. ¿Deseas continuar?',
        confirmButtonText: 'Eliminar vale',
    });

    if (!confirmed) return;
    deleting.value = true;

    try {
        const { data } = await axios.delete(route('vales-almacen.destroy', vale.id));
        vales.value = vales.value.filter((item) => item.id !== vale.id);
        idVisualizacion.value = vales.value[0]?.id ?? null;
        iframeLoading.value = idVisualizacion.value !== null;
        iframeVersion.value++;
        MyBasicToast.success(data.message);
    } catch (error) {
        MyBasicToast.error(
            axios.isAxiosError(error)
                ? (error.response?.data?.message ?? 'No fue posible eliminar el vale de almacén.')
                : 'No fue posible eliminar el vale de almacén.',
        );
    } finally {
        deleting.value = false;
    }
};

defineExpose({ Open });
</script>

<template>
    <BaseModal
        :show="show"
        :loading="loading"
        :saving="busy"
        :modal-title="title"
        modal-description="Consulta y administración de vales de almacén"
        loading-message="Cargando vales de almacén"
        saving-message="Se está procesando el vale"
        modal-class="!w-[calc(100vw-1rem)] !max-w-[96rem]"
        classslot="w-full !overflow-hidden !px-0"
        @close="close"
    >
        <div class="flex min-h-0 flex-col">
            <div class="flex flex-none items-center gap-1 overflow-x-auto border-y border-gray-200 bg-white py-2">
                <Button
                    v-if="canCreate"
                    text="Nuevo vale"
                    icon="fa-solid fa-circle-plus"
                    :type="idVisualizacion === null && !editingVale ? 'new' : 'secondary'"
                    :disabled="busy"
                    class="flex-none"
                    @click="openCreate"
                />
                <Button
                    v-for="(vale, index) in vales"
                    :key="vale.id"
                    :text="`Vale ${vales.length - index}`"
                    icon="fa-solid fa-file"
                    :type="idVisualizacion === vale.id || editingVale?.id === vale.id ? 'delete' : 'secondary'"
                    :disabled="busy"
                    :title="`Folio ${vale.folio} · ${vale.estatus} · ${vale.fecha}`"
                    class="flex-none"
                    @click="selectVale(vale)"
                />
            </div>

            <ValeAlmacenForm
                v-if="idVisualizacion === null && orden && (canCreate || editingVale)"
                v-model:saving="formSaving"
                :orden="orden"
                :vale="editingVale"
                @cancel="cancelForm"
                @saved="handleValeSaved"
            />

            <div v-else-if="selectedVale" class="relative h-[calc(90vh-11rem)] min-h-[420px] overflow-hidden bg-[#333]">
                <Loading v-if="iframeLoading && selectedVale" class="absolute inset-0 z-10 bg-white" text="Cargando vale" />
                <iframe
                    :key="`${selectedVale.id}-${iframeVersion}`"
                    :src="selectedPdfUrl"
                    class="h-full w-full border-0"
                    title="Vista previa del vale de almacén"
                    @load="iframeLoading = false"
                />
            </div>

            <div v-else class="flex h-[calc(90vh-11rem)] min-h-[420px] items-center justify-center bg-[#333] px-6 text-center text-lg text-white">
                <span v-if="loadError">{{ loadError }}</span>
                <span v-else>Aún no hay vales de almacén.</span>
            </div>
        </div>

        <template #footer>
            <Button
                v-if="canDelete && selectedVale"
                type="delete"
                text="Eliminar"
                icon="fa-solid fa-trash"
                :disabled="busy"
                @click="deleteSelectedVale"
            />
            <Button
                v-if="canEditSelected"
                type="new"
                text="Editar"
                icon="fa-solid fa-pen"
                :disabled="busy"
                @click="openEdit"
            />
        </template>
    </BaseModal>
</template>
