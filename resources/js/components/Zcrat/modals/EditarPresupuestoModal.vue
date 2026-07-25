<script setup lang="ts">
import Select from '@/components/Zcrat/Elements/Select.vue';
import Table from '@/components/Zcrat/Elements/Table.vue';
import Button from '@/components/Zcrat/Inputs/Button.vue';
import InputBasic from '@/components/Zcrat/Inputs/form/InputBasic.vue';
import Textarea from '@/components/Zcrat/Inputs/form/Textarea.vue';
import AgregarConceptosPresupuestoModal from '@/components/Zcrat/modals/AgregarConceptosPresupuestoModal.vue';
import BaseModal from '@/components/Zcrat/modals/BaseModal.vue';
import type { option } from '@/types/generales';
import type { buttonconfirmed } from '@/types/modals';
import MyBasicToast from '@/utils/ToastNotificationBasic';
import axios from 'axios';
import { computed, onMounted, reactive, ref, watch } from 'vue';

interface ConceptoPresupuesto {
    id: number;
    concepto_id: number;
    descripcion: string;
    categoria: string;
    cantidad: string | number;
    costo: string | number;
    venta: string | number;
}

interface PresupuestoResponse {
    presupuesto: {
        id: number;
        folio: string;
        tipo_id: number;
        vigencia: string | null;
        garantia: string;
        observaciones: string;
        descripcion_mo: string;
        orden: string;
        modulo: string;
        vehiculo: string;
        empresa: string;
        unidad: string;
    };
    conceptos: ConceptoPresupuesto[];
}

const props = defineProps<{
    show: boolean;
    presupuestoId: number | null;
}>();

const emit = defineEmits<{
    (event: 'close'): void;
    (event: 'saved'): void;
}>();

const loading = ref(false);
const saving = ref(false);
const showConcepts = ref(false);
const tipos = ref<option[]>([]);
const conceptos = ref<ConceptoPresupuesto[]>([]);
const summary = reactive({
    orden: '',
    modulo: '',
    vehiculo: '',
    empresa: '',
    unidad: '',
});
const form = reactive({
    folio: '',
    tipo_id: null as number | null,
    vigencia: null as string | null,
    garantia: '',
    observaciones: '',
    descripcion_mo: '',
});

const escapeHtml = (value: string | number | null) =>
    String(value ?? '')
        .replaceAll('&', '&amp;')
        .replaceAll('<', '&lt;')
        .replaceAll('>', '&gt;')
        .replaceAll('"', '&quot;')
        .replaceAll("'", '&#039;');

const formatCurrency = (value: string | number) =>
    new Intl.NumberFormat('es-MX', {
        style: 'currency',
        currency: 'MXN',
    }).format(Number(value) || 0);

const loadTipos = async () => {
    try {
        const response = await axios.get<{ options: option[] }>(route('select.tipos'), {
            params: { categoria_id: 2 },
        });
        tipos.value = response.data.options ?? [];
    } catch {
        tipos.value = [];
        MyBasicToast.error('No fue posible cargar los tipos de presupuesto');
    }
};

const load = async () => {
    if (!props.presupuestoId) return;
    loading.value = true;

    try {
        const response = await axios.get<PresupuestoResponse>(route('presupuesto.show', props.presupuestoId));
        const data = response.data;
        Object.assign(form, {
            folio: data.presupuesto.folio,
            tipo_id: data.presupuesto.tipo_id,
            vigencia: data.presupuesto.vigencia,
            garantia: data.presupuesto.garantia,
            observaciones: data.presupuesto.observaciones,
            descripcion_mo: data.presupuesto.descripcion_mo,
        });
        Object.assign(summary, {
            orden: data.presupuesto.orden,
            modulo: data.presupuesto.modulo,
            vehiculo: data.presupuesto.vehiculo,
            empresa: data.presupuesto.empresa,
            unidad: data.presupuesto.unidad,
        });
        conceptos.value = data.conceptos;
    } catch {
        MyBasicToast.error('No fue posible obtener el presupuesto');
        emit('close');
    } finally {
        loading.value = false;
    }
};

const save = async () => {
    if (!props.presupuestoId) return;
    saving.value = true;

    try {
        const response = await axios.put(route('presupuesto.update', props.presupuestoId), {
            folio: form.folio,
            vigencia: form.vigencia,
            garantia: form.garantia,
            observaciones: form.observaciones,
            descripcion_mo: form.descripcion_mo,
        });
        MyBasicToast.success(response.data.message);
        emit('saved');
        emit('close');
    } catch (error) {
        if (axios.isAxiosError(error) && error.response?.status === 422) {
            MyBasicToast.error('Revisa los datos del presupuesto');
        } else {
            MyBasicToast.error('No fue posible actualizar el presupuesto');
        }
    } finally {
        saving.value = false;
    }
};

const buttonConfirm = computed<buttonconfirmed>(() => ({
    text: saving.value ? 'Guardando...' : 'Guardar cambios',
    classname: 'bg-blue-700 text-white',
    disabled: saving.value || loading.value,
    onClick: save,
}));

watch(
    () => [props.show, props.presupuestoId],
    ([show]) => {
        if (show) load();
    },
);

onMounted(loadTipos);
</script>

<template>
    <BaseModal
        :show="show"
        modaltitle="Modificar presupuesto"
        modaldescription="Actualiza el presupuesto y administra sus conceptos"
        position="center"
        :loading="loading || saving"
        :buttonconfirm="buttonConfirm"
        @close="emit('close')"
    >
        <div class="flex w-[min(72rem,calc(100vw-3rem))] flex-col gap-4 pb-2">
            <div class="grid grid-cols-1 gap-2 rounded-lg bg-gray-100 p-3 text-sm sm:grid-cols-2 lg:grid-cols-5">
                <div>
                    <span class="block text-gray-500">Orden</span>
                    <span class="font-medium">{{ summary.orden || 'Sin orden' }}</span>
                </div>
                <div>
                    <span class="block text-gray-500">Módulo</span>
                    <span class="font-medium">{{ summary.modulo || 'Sin módulo' }}</span>
                </div>
                <div>
                    <span class="block text-gray-500">Vehículo de conceptos</span>
                    <span class="font-medium">{{ summary.vehiculo || 'Sin vehículo' }}</span>
                </div>
                <div>
                    <span class="block text-gray-500">Empresa</span>
                    <span class="font-medium">{{ summary.empresa || 'Sin empresa' }}</span>
                </div>
                <div>
                    <span class="block text-gray-500">Unidad</span>
                    <span class="font-medium">{{ summary.unidad || 'Sin unidad' }}</span>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-3">
                <InputBasic v-model="form.folio" id="presupuesto_folio" label="Folio" />
                <Select
                    v-model="form.tipo_id"
                    id="presupuesto_tipo"
                    label="Tipo"
                    :options="tipos"
                    disabled
                />
                <div class="flex flex-col">
                    <label for="presupuesto_vigencia">Vigencia</label>
                    <input id="presupuesto_vigencia" v-model="form.vigencia" type="date" class="w-full rounded-md border-gray-500" />
                </div>
            </div>

            <div class="grid grid-cols-1 gap-3 lg:grid-cols-3">
                <Textarea v-model="form.descripcion_mo" id="presupuesto_descripcion" label="Descripción de mano de obra" classname="h-24" />
                <Textarea v-model="form.garantia" id="presupuesto_garantia" label="Garantía" classname="h-24" />
                <Textarea v-model="form.observaciones" id="presupuesto_observaciones" label="Observaciones" classname="h-24" />
            </div>

            <section class="rounded-lg border border-gray-200">
                <div class="flex flex-wrap items-center justify-between gap-2 border-b border-gray-200 px-3 py-2">
                    <div>
                        <h3 class="font-semibold">Conceptos del presupuesto</h3>
                        <p class="text-sm text-gray-500">{{ conceptos.length }} conceptos agregados</p>
                    </div>
                    <Button v-if="presupuestoId" text="Agregar conceptos" type="save" icon="fa-solid fa-circle-plus" @click="showConcepts = true" />
                </div>

                <Table
                    v-if="conceptos.length > 0"
                    :titles="['Descripción', 'Categoría', 'Cantidad', 'Costo', 'Venta']"
                    :rows="
                        conceptos.map((concepto) => ({
                            columns: [
                                { element: escapeHtml(concepto.descripcion), classname: 'normal-case' },
                                { element: escapeHtml(concepto.categoria), classname: 'normal-case' },
                                { element: concepto.cantidad, classname: 'text-right' },
                                { element: formatCurrency(concepto.costo), classname: 'whitespace-nowrap text-right' },
                                { element: formatCurrency(concepto.venta), classname: 'whitespace-nowrap text-right' },
                            ],
                        }))
                    "
                />
                <div v-else class="p-8 text-center text-gray-500">Este presupuesto todavía no tiene conceptos.</div>
            </section>
        </div>
    </BaseModal>

    <AgregarConceptosPresupuestoModal :show="showConcepts" :presupuesto-id="presupuestoId" @close="showConcepts = false" @added="load" />
</template>
