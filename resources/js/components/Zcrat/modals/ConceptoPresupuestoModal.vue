<script setup lang="ts">
import ZDRemoteSelect from '@/components/Zcrat/Elements/ZDRemoteSelect.vue';
import InputBasic from '@/components/Zcrat/Inputs/form/InputBasic.vue';
import Textarea from '@/components/Zcrat/Inputs/form/Textarea.vue';
import BaseModal from '@/components/Zcrat/modals/BaseModal.vue';
import type { option } from '@/types/generales';
import type { ConfirmButton } from '@/types/modals';
import MyBasicToast from '@/utils/ToastNotificationBasic';
import axios from 'axios';
import { computed, reactive, ref, watch } from 'vue';

interface ConceptoForm {
    numero: string;
    descripcion: string;
    garantia_dias: number | null;
    tipo_id: number | null;
    modulo_id: number | null;
    categoria_sat_id: number | null;
    unidad_sat_id: number | null;
    vehiculo_id: number | null;
    p_refaccion: number;
    p_mano_obra: number;
}

interface ContextoPresupuesto {
    modulo: option | null;
    vehiculo: option | null;
}

const props = withDefaults(defineProps<{
    show: boolean;
    costoId: number | null;
    presupuestoId?: number | null;
    contextoPresupuesto?: ContextoPresupuesto | null;
}>(), {
    presupuestoId: null,
    contextoPresupuesto: null,
});

const emit = defineEmits<{
    (event: 'close'): void;
    (event: 'saved'): void;
}>();

const emptyForm = (): ConceptoForm => ({
    numero: '',
    descripcion: '',
    garantia_dias: null,
    tipo_id: null,
    modulo_id: null,
    categoria_sat_id: null,
    unidad_sat_id: null,
    vehiculo_id: null,
    p_refaccion: 0,
    p_mano_obra: 0,
});

const form = reactive<ConceptoForm>(emptyForm());
const errors = ref<Record<string, string[]>>({});
const loading = ref(false);
const selectedCategoria = ref<option | null>(null);
const selectedModulo = ref<option | null>(null);
const selectedCategoriaSat = ref<option | null>(null);
const selectedUnidadSat = ref<option | null>(null);
const selectedVehiculo = ref<option | null>(null);

const isBudgetContext = computed(() => props.presupuestoId !== null && props.costoId === null);
const total = computed(() => Number(form.p_refaccion || 0) + Number(form.p_mano_obra || 0));
const vehiculoParams = computed(() => ({
    modulo_id: form.modulo_id,
}));
const categoriaEndpoint = computed(() =>
    isBudgetContext.value
        ? 'select2.presupuesto.categorias-conceptos'
        : 'select2.catalogo.categorias-conceptos',
);
const categoriaParams = computed(() =>
    isBudgetContext.value
        ? { presupuesto_id: props.presupuestoId }
        : {},
);

const formatCurrency = (value: number) =>
    new Intl.NumberFormat('es-MX', {
        style: 'currency',
        currency: 'MXN',
    }).format(value);

const resetForm = () => {
    Object.assign(form, emptyForm());
    selectedCategoria.value = null;
    selectedModulo.value = null;
    selectedCategoriaSat.value = null;
    selectedUnidadSat.value = null;
    selectedVehiculo.value = null;
    errors.value = {};

    if (isBudgetContext.value && props.contextoPresupuesto) {
        selectedModulo.value = props.contextoPresupuesto.modulo;
        selectedVehiculo.value = props.contextoPresupuesto.vehiculo;
        form.modulo_id = props.contextoPresupuesto.modulo
            ? Number(props.contextoPresupuesto.modulo.value)
            : null;
        form.vehiculo_id = props.contextoPresupuesto.vehiculo
            ? Number(props.contextoPresupuesto.vehiculo.value)
            : null;
    }
};

const clearError = (field: string) => {
    delete errors.value[field];
};

watch(selectedCategoria, (value) => {
    form.tipo_id = value ? Number(value.value) : null;
});
watch(selectedModulo, (value) => {
    form.modulo_id = value ? Number(value.value) : null;
});
watch(selectedCategoriaSat, (value) => {
    form.categoria_sat_id = value ? Number(value.value) : null;
});
watch(selectedUnidadSat, (value) => {
    form.unidad_sat_id = value ? Number(value.value) : null;
});
watch(selectedVehiculo, (value) => {
    form.vehiculo_id = value ? Number(value.value) : null;
});
watch(
    () => form.modulo_id,
    (value, previousValue) => {
        if (previousValue !== null && value !== previousValue) {
            selectedVehiculo.value = null;
            form.vehiculo_id = null;
        }
    },
);

const loadData = async () => {
    loading.value = true;
    resetForm();

    try {
        if (props.costoId !== null) {
            const response = await axios.get(route('catalogos.conceptos.show', props.costoId));
            Object.assign(form, {
                numero: response.data.numero,
                descripcion: response.data.descripcion,
                garantia_dias: response.data.garantia_dias,
                p_refaccion: Number(response.data.p_refaccion),
                p_mano_obra: Number(response.data.p_mano_obra),
            });

            selectedCategoria.value = response.data.tipo;
            selectedModulo.value = response.data.modulo;
            selectedCategoriaSat.value = response.data.categoria_sat;
            selectedUnidadSat.value = response.data.unidad_sat;
            selectedVehiculo.value = response.data.vehiculo;
        }
    } catch {
        MyBasicToast.error('No fue posible cargar los datos del concepto');
        emit('close');
    } finally {
        loading.value = false;
    }
};

watch(
    () => props.show,
    (show) => {
        if (show) loadData();
    },
);

const save = async () => {
    loading.value = true;
    errors.value = {};

    try {
        if (isBudgetContext.value && props.presupuestoId !== null) {
            await axios.post(route('presupuesto.conceptos.crear', props.presupuestoId), form);
            MyBasicToast.success('Concepto creado y agregado al presupuesto');
        } else if (props.costoId === null) {
            await axios.post(route('catalogos.conceptos.store'), form);
            MyBasicToast.success('Concepto creado correctamente');
        } else {
            await axios.put(route('catalogos.conceptos.update', props.costoId), form);
            MyBasicToast.success('Concepto actualizado correctamente');
        }

        emit('saved');
        emit('close');
    } catch (error) {
        if (axios.isAxiosError(error) && error.response?.status === 422) {
            errors.value = error.response.data.errors ?? {};
            MyBasicToast.error('Revisa los datos capturados');
        } else {
            MyBasicToast.error('No fue posible guardar el concepto');
        }
    } finally {
        loading.value = false;
    }
};

const confirmButton = computed<ConfirmButton>(() => ({
    text: props.costoId === null ? 'Crear concepto' : 'Guardar cambios',
    className: 'bg-blue-700 text-white',
    disabled:
        loading.value ||
        !form.numero ||
        !form.descripcion ||
        form.tipo_id === null ||
        form.modulo_id === null ||
        form.categoria_sat_id === null ||
        form.unidad_sat_id === null ||
        form.vehiculo_id === null,
    onClick: save,
}));

const fieldError = (field: string) => errors.value[field]?.[0];
</script>

<template>
    <BaseModal
        :show="show"
        :saving="loading"
        saving-message="Espera a que termine la operación"
        :modal-title="isBudgetContext ? 'Nuevo concepto para presupuesto' : costoId === null ? 'Nuevo concepto' : 'Modificar concepto'"
        :modal-description="isBudgetContext ? 'El módulo y el vehículo están definidos por la orden del presupuesto' : undefined"
        position="center"
        :z-index-class="isBudgetContext ? 'z-[999]' : 'z-[50]'"
        :confirm-button="confirmButton"
        @close="emit('close')"
    >
        <div class="grid w-[min(92vw,70rem)] grid-cols-1 gap-3 py-2 sm:grid-cols-2 lg:grid-cols-4">
            <InputBasic id="numero_concepto" v-model="form.numero" label="Número" :errors="errors.numero" />

            <InputBasic id="garantia_dias" v-model="form.garantia_dias" type="number" label="Garantía (días)" :errors="errors.garantia_dias" />

            <div class="self-end rounded-md bg-blue-50 p-2 text-right">
                <span class="block text-xs text-gray-600">Total</span>
                <strong>{{ formatCurrency(total) }}</strong>
            </div>

            <Textarea
                id="descripcion_concepto"
                v-model="form.descripcion"
                classdiv="sm:col-span-2 lg:col-span-4"
                classname="min-h-20"
                label="Descripción"
            />
            <p v-if="fieldError('descripcion')" class="-mt-2 text-sm text-red-600 sm:col-span-2 lg:col-span-4">
                {{ fieldError('descripcion') }}
            </p>

            <ZDRemoteSelect
                v-model="selectedCategoria"
                :endpoint="categoriaEndpoint"
                label="Categoría"
                placeholder="Buscar categoría"
                :params="categoriaParams"
                :cacheoptions="false"
                :errors="errors.tipo_id"
                :DeleteErrors="() => clearError('tipo_id')"
            />

            <ZDRemoteSelect
                v-model="selectedModulo"
                endpoint="select2.catalogo.modulos-orden"
                label="Módulo"
                placeholder="Buscar módulo"
                :cacheoptions="false"
                :disabled="isBudgetContext"
                :errors="errors.modulo_id"
                :DeleteErrors="() => clearError('modulo_id')"
            />

            <ZDRemoteSelect
                v-model="selectedCategoriaSat"
                endpoint="select2.catalogo.categorias-sat"
                label="Categoría SAT"
                placeholder="Descripción o código SAT"
                :cacheoptions="false"
                :errors="errors.categoria_sat_id"
                :DeleteErrors="() => clearError('categoria_sat_id')"
            />

            <ZDRemoteSelect
                v-model="selectedUnidadSat"
                endpoint="select2.catalogo.unidades-sat"
                label="Unidad SAT"
                placeholder="Descripción o código"
                :cacheoptions="false"
                :errors="errors.unidad_sat_id"
                :DeleteErrors="() => clearError('unidad_sat_id')"
            />

            <ZDRemoteSelect
                v-model="selectedVehiculo"
                endpoint="select2.catalogo.vehiculos-conceptos"
                label="Vehículo"
                placeholder="Buscar vehículo"
                classDiv="sm:col-span-2"
                :params="vehiculoParams"
                :disabled="isBudgetContext || form.modulo_id === null"
                :cacheoptions="false"
                :errors="errors.vehiculo_id"
                :DeleteErrors="() => clearError('vehiculo_id')"
            />

            <InputBasic id="precio_refaccion" v-model="form.p_refaccion" type="number" label="Precio refacción" :errors="errors.p_refaccion" />

            <InputBasic id="precio_mano_obra" v-model="form.p_mano_obra" type="number" label="Precio mano de obra" :errors="errors.p_mano_obra" />
        </div>
    </BaseModal>
</template>
