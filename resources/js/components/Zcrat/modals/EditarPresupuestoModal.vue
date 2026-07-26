<script setup lang="ts">
import Select from '@/components/Zcrat/Elements/Select.vue';
import Table from '@/components/Zcrat/Elements/Table.vue';
import Button from '@/components/Zcrat/Inputs/Button.vue';
import InputBasic from '@/components/Zcrat/Inputs/form/InputBasic.vue';
import Textarea from '@/components/Zcrat/Inputs/form/Textarea.vue';
import AgregarConceptosPresupuestoModal from '@/components/Zcrat/modals/AgregarConceptosPresupuestoModal.vue';
import BaseModal from '@/components/Zcrat/modals/BaseModal.vue';
import ConceptoPresupuestoModal from '@/components/Zcrat/modals/ConceptoPresupuestoModal.vue';
import { useAuth } from '@/composables/useAuth';
import type { option } from '@/types/generales';
import type { buttonconfirmed } from '@/types/modals';
import MyBasicToast from '@/utils/ToastNotificationBasic';
import axios from 'axios';
import { computed, defineComponent, h, onMounted, reactive, ref, watch } from 'vue';

interface ConceptoPresupuesto {
    id: number;
    concepto_id: number;
    descripcion: string;
    categoria: string;
    cantidad: string | number;
    costo: string | number;
    venta: string | number | null;
    subtotal: string | number;
}

type ConceptoValueField = 'cantidad' | 'costo' | 'venta';

interface OriginalConceptValues {
    cantidad: number;
    costo: number;
    venta: number | null;
    subtotal: number;
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
        modulo_id: number | null;
        modulo: string;
        vehiculo_concepto_id: number | null;
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

const { can } = useAuth();
const loading = ref(false);
const saving = ref(false);
const savingConcepts = ref(false);
const showConcepts = ref(false);
const showCreateConcept = ref(false);
const tipos = ref<option[]>([]);
const conceptos = ref<ConceptoPresupuesto[]>([]);
const originalConceptValues = ref<Record<number, OriginalConceptValues>>({});
const summary = reactive({
    orden: '',
    modulo_id: null as number | null,
    modulo: '',
    vehiculo_concepto_id: null as number | null,
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
const conceptContext = computed(() => ({
    modulo: summary.modulo_id
        ? { value: summary.modulo_id, label: summary.modulo }
        : null,
    vehiculo: summary.vehiculo_concepto_id
        ? { value: summary.vehiculo_concepto_id, label: summary.vehiculo }
        : null,
}));
const canViewSale = computed(() => can('ver_venta_presupuestos'));

const EditableNumberInput = defineComponent({
    props: {
        value: {
            type: [Number, String],
            required: true,
        },
        min: {
            type: Number,
            required: true,
        },
        step: {
            type: Number,
            required: true,
        },
        currency: {
            type: Boolean,
            default: false,
        },
        label: {
            type: String,
            required: true,
        },
    },
    emits: ['valueChange'],
    setup(componentProps, { emit: componentEmit }) {
        return () =>
            h('div', { class: 'flex min-w-28 items-center rounded-md border border-gray-400 bg-white focus-within:border-blue-600' }, [
                ...(componentProps.currency
                    ? [h('span', { class: 'border-r border-gray-300 px-2 text-gray-500' }, '$')]
                    : []),
                h('input', {
                    value: componentProps.value,
                    type: 'number',
                    min: componentProps.min,
                    step: componentProps.step,
                    inputmode: 'decimal',
                    'aria-label': componentProps.label,
                    class: 'w-24 rounded-md border-0 bg-transparent px-2 py-1 text-right focus:ring-0',
                    onInput: (event: Event) =>
                        componentEmit('valueChange', (event.target as HTMLInputElement).value),
                }),
            ]);
    },
});

const escapeHtml = (value: string | number | null) =>
    String(value ?? '')
        .replaceAll('&', '&amp;')
        .replaceAll('<', '&lt;')
        .replaceAll('>', '&gt;')
        .replaceAll('"', '&quot;')
        .replaceAll("'", '&#039;');

const numberValue = (value: string | number) =>
    String(value).trim() === '' ? Number.NaN : Number(value);

const changedConcepts = computed(() =>
    conceptos.value.filter((concepto) => {
        const original = originalConceptValues.value[concepto.id];

        return (
            !original ||
            numberValue(concepto.cantidad) !== original.cantidad ||
            numberValue(concepto.costo) !== original.costo ||
            (
                canViewSale.value &&
                numberValue(concepto.venta ?? '') !== original.venta
            )
        );
    }),
);
const hasInvalidConceptValues = computed(() =>
    conceptos.value.some(
        (concepto) =>
            !Number.isFinite(numberValue(concepto.cantidad)) ||
            !Number.isInteger(numberValue(concepto.cantidad)) ||
            numberValue(concepto.cantidad) < 1 ||
            !Number.isFinite(numberValue(concepto.costo)) ||
            numberValue(concepto.costo) < 0 ||
            !Number.isInteger(numberValue(concepto.costo) * 2) ||
            (
                canViewSale.value &&
                (
                    !Number.isFinite(numberValue(concepto.venta ?? '')) ||
                    numberValue(concepto.venta ?? '') < 0 ||
                    !Number.isInteger(numberValue(concepto.venta ?? '') * 2)
                )
            ),
    ),
);
const hasConceptChanges = computed(() => changedConcepts.value.length > 0);
const conceptoSubtotal = (concepto: ConceptoPresupuesto) => {
    const quantity = numberValue(concepto.cantidad);
    if (!Number.isFinite(quantity)) return 0;

    if (canViewSale.value) {
        const sale = numberValue(concepto.venta ?? '');

        return Number.isFinite(sale) ? quantity * sale : 0;
    }

    const original = originalConceptValues.value[concepto.id];
    if (!original || original.cantidad <= 0) return numberValue(concepto.subtotal);

    return quantity * (original.subtotal / original.cantidad);
};
const totalPresupuesto = computed(() =>
    conceptos.value.reduce(
        (total, concepto) => total + conceptoSubtotal(concepto),
        0,
    ),
);

const formatCurrency = (value: string | number) =>
    new Intl.NumberFormat('es-MX', {
        style: 'currency',
        currency: 'MXN',
    }).format(Number(value) || 0);

const updateConceptValue = (
    conceptoId: number,
    field: ConceptoValueField,
    value: string,
) => {
    const concepto = conceptos.value.find((item) => item.id === conceptoId);
    if (concepto) concepto[field] = value;
};

const snapshotConceptValues = () => {
    originalConceptValues.value = Object.fromEntries(
        conceptos.value.map((concepto) => [
            concepto.id,
            {
                cantidad: numberValue(concepto.cantidad),
                costo: numberValue(concepto.costo),
                venta: concepto.venta === null ? null : numberValue(concepto.venta),
                subtotal: numberValue(concepto.subtotal),
            },
        ]),
    );
};

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
            modulo_id: data.presupuesto.modulo_id,
            modulo: data.presupuesto.modulo,
            vehiculo_concepto_id: data.presupuesto.vehiculo_concepto_id,
            vehiculo: data.presupuesto.vehiculo,
            empresa: data.presupuesto.empresa,
            unidad: data.presupuesto.unidad,
        });
        conceptos.value = data.conceptos;
        snapshotConceptValues();
    } catch {
        MyBasicToast.error('No fue posible obtener el presupuesto');
        emit('close');
    } finally {
        loading.value = false;
    }
};

const saveConcepts = async () => {
    if (!props.presupuestoId || !hasConceptChanges.value) return;
    if (hasInvalidConceptValues.value) {
        MyBasicToast.error('La cantidad debe ser un entero y los precios deben avanzar en incrementos de $0.50');
        return;
    }

    savingConcepts.value = true;

    try {
        const response = await axios.put(
            route('presupuesto.conceptos.update', props.presupuestoId),
            {
                conceptos: changedConcepts.value.map((concepto) => ({
                    id: concepto.id,
                    cantidad: numberValue(concepto.cantidad),
                    costo: numberValue(concepto.costo),
                    ...(canViewSale.value
                        ? { venta: numberValue(concepto.venta ?? '') }
                        : {}),
                })),
            },
        );
        MyBasicToast.success(response.data.message);
        await load();
        emit('saved');
    } catch (error) {
        if (axios.isAxiosError(error) && error.response?.status === 422) {
            MyBasicToast.error(error.response.data.message || 'Revisa las cantidades y precios');
        } else {
            MyBasicToast.error('No fue posible actualizar los conceptos del presupuesto');
        }
    } finally {
        savingConcepts.value = false;
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
        :loading="loading || saving || savingConcepts"
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
                    <div class="flex flex-wrap gap-2">
                        <Button
                            v-if="presupuestoId"
                            :text="savingConcepts ? 'Actualizando...' : 'Actualizar conceptos'"
                            type="secondary"
                            icon="fa-solid fa-floppy-disk"
                            :disabled="savingConcepts || !hasConceptChanges || hasInvalidConceptValues"
                            @click="saveConcepts"
                        />
                        <Button
                            v-if="presupuestoId && can('crear_catalogo_conceptos')"
                            text="Crear concepto"
                            type="save"
                            icon="fa-solid fa-file-circle-plus"
                            @click="showCreateConcept = true"
                        />
                        <Button
                            v-if="presupuestoId"
                            text="Agregar conceptos"
                            type="save"
                            icon="fa-solid fa-circle-plus"
                            @click="showConcepts = true"
                        />
                    </div>
                </div>

                <Table
                    v-if="conceptos.length > 0"
                    :titles="[
                        'Descripción',
                        'Categoría',
                        {
                            title: 'Valores del presupuesto',
                            subtittles: [
                                { title: 'Cantidad' },
                                { title: 'Costo' },
                                ...(canViewSale ? [{ title: 'Venta' }] : []),
                                { title: 'Subtotal' },
                            ],
                        },
                    ]"
                    :rows="
                        conceptos.map((concepto) => ({
                            columns: [
                                { element: escapeHtml(concepto.descripcion), classname: 'normal-case' },
                                { element: escapeHtml(concepto.categoria), classname: 'normal-case' },
                                {
                                    element: EditableNumberInput,
                                    props: {
                                        value: concepto.cantidad,
                                        min: 1,
                                        step: 1,
                                        label: `Cantidad de ${concepto.descripcion}`,
                                        onValueChange: (value: string) => updateConceptValue(concepto.id, 'cantidad', value),
                                    },
                                },
                                {
                                    element: EditableNumberInput,
                                    props: {
                                        value: concepto.costo,
                                        min: 0,
                                        step: 0.5,
                                        currency: true,
                                        label: `Costo de ${concepto.descripcion}`,
                                        onValueChange: (value: string) => updateConceptValue(concepto.id, 'costo', value),
                                    },
                                },
                                ...(canViewSale
                                    ? [
                                          {
                                              element: EditableNumberInput,
                                              props: {
                                                  value: concepto.venta ?? 0,
                                                  min: 0,
                                                  step: 0.5,
                                                  currency: true,
                                                  label: `Venta de ${concepto.descripcion}`,
                                                  onValueChange: (value: string) =>
                                                      updateConceptValue(concepto.id, 'venta', value),
                                              },
                                          },
                                      ]
                                    : []),
                                {
                                    element: formatCurrency(conceptoSubtotal(concepto)),
                                    classname: 'whitespace-nowrap text-right font-medium',
                                },
                            ],
                        }))
                    "
                />
                <div
                    v-if="conceptos.length > 0"
                    class="flex items-center justify-end gap-4 border-t border-gray-300 bg-gray-50 px-4 py-3 text-right"
                >
                    <span class="font-semibold uppercase text-gray-600">Total</span>
                    <strong class="min-w-32 text-lg">{{ formatCurrency(totalPresupuesto) }}</strong>
                </div>
                <div v-else class="p-8 text-center text-gray-500">Este presupuesto todavía no tiene conceptos.</div>
            </section>
        </div>
    </BaseModal>

    <AgregarConceptosPresupuestoModal :show="showConcepts" :presupuesto-id="presupuestoId" @close="showConcepts = false" @added="load" />
    <ConceptoPresupuestoModal
        :show="showCreateConcept"
        :costo-id="null"
        :presupuesto-id="presupuestoId"
        :contexto-presupuesto="conceptContext"
        @close="showCreateConcept = false"
        @saved="load"
    />
</template>
