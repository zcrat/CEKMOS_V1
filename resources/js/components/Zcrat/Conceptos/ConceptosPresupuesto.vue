<script setup lang="ts">
import Table from '@/components/Zcrat/Elements/Table.vue';
import Button from '@/components/Zcrat/Inputs/Button.vue';
import AgregarConceptosPresupuestoModal from '@/components/Zcrat/modals/AgregarConceptosPresupuestoModal.vue';
import ConceptoPresupuestoModal from '@/components/Zcrat/modals/ConceptoPresupuestoModal.vue';
import { useAuth } from '@/composables/useAuth';
import type {
    ConceptoPresupuestoAsignado,
    option,
} from '@/types/generales';
import MyBasicToast from '@/utils/ToastNotificationBasic';
import axios from 'axios';
import { computed, defineComponent, h, ref, watch } from 'vue';

type ConceptoValueField = 'cantidad' | 'costo' | 'venta';

interface OriginalConceptValues {
    cantidad: number;
    costo: number;
    venta: number | null;
    subtotal: number;
}

const props = defineProps<{
    presupuestoId: number;
    conceptos: ConceptoPresupuestoAsignado[];
    modulo: option | null;
    vehiculo: option | null;
}>();

const emit = defineEmits<{
    (event: 'loading', value: boolean): void;
    (event: 'reload'): void;
    (event: 'saved'): void;
}>();

const { can } = useAuth();
const saving = ref(false);
const showAdd = ref(false);
const showCreate = ref(false);
const items = ref<ConceptoPresupuestoAsignado[]>([]);
const originalValues = ref<Record<number, OriginalConceptValues>>({});
const canViewSale = computed(() => can('ver_venta_presupuestos'));
const budgetContext = computed(() => ({
    modulo: props.modulo,
    vehiculo: props.vehiculo,
}));

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
            h('div', {
                class: 'flex min-w-28 items-center rounded-md border border-gray-400 bg-white focus-within:border-blue-600',
            }, [
                ...(componentProps.currency
                    ? [
                        h('span', {
                            class: 'border-r border-gray-300 px-2 text-gray-500',
                        }, '$'),
                    ]
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
                        componentEmit(
                            'valueChange',
                            (event.target as HTMLInputElement).value,
                        ),
                }),
            ]);
    },
});

const numberValue = (value: string | number) =>
    String(value).trim() === '' ? Number.NaN : Number(value);

const escapeHtml = (value: string | number | null) =>
    String(value ?? '')
        .replaceAll('&', '&amp;')
        .replaceAll('<', '&lt;')
        .replaceAll('>', '&gt;')
        .replaceAll('"', '&quot;')
        .replaceAll("'", '&#039;');

const snapshotValues = () => {
    originalValues.value = Object.fromEntries(
        items.value.map((concepto) => [
            concepto.id,
            {
                cantidad: numberValue(concepto.cantidad),
                costo: numberValue(concepto.costo),
                venta: concepto.venta === null
                    ? null
                    : numberValue(concepto.venta),
                subtotal: numberValue(concepto.subtotal),
            },
        ]),
    );
};

watch(
    () => props.conceptos,
    (conceptos) => {
        items.value = conceptos.map((concepto) => ({ ...concepto }));
        snapshotValues();
    },
    { deep: true, immediate: true },
);

const changedConcepts = computed(() =>
    items.value.filter((concepto) => {
        const original = originalValues.value[concepto.id];

        return (
            !original
            || numberValue(concepto.cantidad) !== original.cantidad
            || numberValue(concepto.costo) !== original.costo
            || (
                canViewSale.value
                && numberValue(concepto.venta ?? '') !== original.venta
            )
        );
    }),
);

const hasInvalidValues = computed(() =>
    items.value.some(
        (concepto) =>
            !Number.isFinite(numberValue(concepto.cantidad))
            || !Number.isInteger(numberValue(concepto.cantidad))
            || numberValue(concepto.cantidad) < 1
            || !Number.isFinite(numberValue(concepto.costo))
            || numberValue(concepto.costo) < 0
            || !Number.isInteger(numberValue(concepto.costo) * 2)
            || (
                canViewSale.value
                && (
                    !Number.isFinite(numberValue(concepto.venta ?? ''))
                    || numberValue(concepto.venta ?? '') < 0
                    || !Number.isInteger(numberValue(concepto.venta ?? '') * 2)
                )
            ),
    ),
);

const conceptoSubtotal = (concepto: ConceptoPresupuestoAsignado) => {
    const quantity = numberValue(concepto.cantidad);
    if (!Number.isFinite(quantity)) return 0;

    if (canViewSale.value) {
        const sale = numberValue(concepto.venta ?? '');

        return Number.isFinite(sale) ? quantity * sale : 0;
    }

    const original = originalValues.value[concepto.id];
    if (!original || original.cantidad <= 0) {
        return numberValue(concepto.subtotal);
    }

    return quantity * (original.subtotal / original.cantidad);
};

const total = computed(() =>
    items.value.reduce(
        (amount, concepto) => amount + conceptoSubtotal(concepto),
        0,
    ),
);

const formatCurrency = (value: string | number) =>
    new Intl.NumberFormat('es-MX', {
        style: 'currency',
        currency: 'MXN',
    }).format(Number(value) || 0);

const updateValue = (
    conceptoId: number,
    field: ConceptoValueField,
    value: string,
) => {
    const concepto = items.value.find((item) => item.id === conceptoId);
    if (concepto) concepto[field] = value;
};

const notifyMutation = () => {
    emit('reload');
    emit('saved');
};

const save = async () => {
    if (changedConcepts.value.length === 0) return;
    if (hasInvalidValues.value) {
        MyBasicToast.error(
            'La cantidad debe ser un entero y los precios deben avanzar en incrementos de $0.50',
        );
        return;
    }

    saving.value = true;
    emit('loading', true);

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
        notifyMutation();
    } catch (error) {
        if (axios.isAxiosError(error) && error.response?.status === 422) {
            MyBasicToast.error(
                error.response.data.message
                    || 'Revisa las cantidades y precios',
            );
        } else {
            MyBasicToast.error(
                'No fue posible actualizar los conceptos del presupuesto',
            );
        }
    } finally {
        saving.value = false;
        emit('loading', false);
    }
};
</script>

<template>
    <section class="rounded-lg border border-gray-200">
        <div class="flex flex-wrap items-center justify-between gap-2 border-b border-gray-200 px-3 py-2">
            <div>
                <h3 class="font-semibold">Conceptos del presupuesto</h3>
                <p class="text-sm text-gray-500">
                    {{ items.length }} conceptos agregados
                </p>
            </div>
            <div class="flex flex-wrap gap-2">
                <Button
                    :text="saving ? 'Actualizando...' : 'Actualizar conceptos'"
                    type="secondary"
                    icon="fa-solid fa-floppy-disk"
                    :disabled="saving || changedConcepts.length === 0 || hasInvalidValues"
                    @click="save"
                />
                <Button
                    v-if="can('crear_catalogo_conceptos')"
                    text="Crear concepto"
                    type="save"
                    icon="fa-solid fa-file-circle-plus"
                    @click="showCreate = true"
                />
                <Button
                    text="Agregar conceptos"
                    type="save"
                    icon="fa-solid fa-circle-plus"
                    @click="showAdd = true"
                />
            </div>
        </div>

        <Table
            v-if="items.length > 0"
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
                items.map((concepto) => ({
                    columns: [
                        {
                            element: escapeHtml(concepto.descripcion),
                            classname: 'normal-case',
                        },
                        {
                            element: escapeHtml(concepto.categoria),
                            classname: 'normal-case',
                        },
                        {
                            element: EditableNumberInput,
                            props: {
                                value: concepto.cantidad,
                                min: 1,
                                step: 1,
                                label: `Cantidad de ${concepto.descripcion}`,
                                onValueChange: (value: string) =>
                                    updateValue(concepto.id, 'cantidad', value),
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
                                onValueChange: (value: string) =>
                                    updateValue(concepto.id, 'costo', value),
                            },
                        },
                        ...(canViewSale
                            ? [{
                                element: EditableNumberInput,
                                props: {
                                    value: concepto.venta ?? 0,
                                    min: 0,
                                    step: 0.5,
                                    currency: true,
                                    label: `Venta de ${concepto.descripcion}`,
                                    onValueChange: (value: string) =>
                                        updateValue(
                                            concepto.id,
                                            'venta',
                                            value,
                                        ),
                                },
                            }]
                            : []),
                        {
                            element: formatCurrency(
                                conceptoSubtotal(concepto),
                            ),
                            classname: 'whitespace-nowrap text-right font-medium',
                        },
                    ],
                }))
            "
        />
        <div
            v-if="items.length > 0"
            class="flex items-center justify-end gap-4 border-t border-gray-300 bg-gray-50 px-4 py-3 text-right"
        >
            <span class="font-semibold uppercase text-gray-600">Total</span>
            <strong class="min-w-32 text-lg">
                {{ formatCurrency(total) }}
            </strong>
        </div>
        <div v-else class="p-8 text-center text-gray-500">
            Este presupuesto todavía no tiene conceptos.
        </div>
    </section>

    <AgregarConceptosPresupuestoModal
        :show="showAdd"
        :presupuesto-id="presupuestoId"
        @close="showAdd = false"
        @added="notifyMutation"
    />
    <ConceptoPresupuestoModal
        :show="showCreate"
        :costo-id="null"
        :presupuesto-id="presupuestoId"
        :contexto-presupuesto="budgetContext"
        @close="showCreate = false"
        @saved="notifyMutation"
    />
</template>
