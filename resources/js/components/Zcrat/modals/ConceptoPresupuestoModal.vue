<script setup lang="ts">
import ZDRemoteSelect from '@/components/Zcrat/Elements/ZDRemoteSelect.vue';
import InputBasic from '@/components/Zcrat/Inputs/form/InputBasic.vue';
import Textarea from '@/components/Zcrat/Inputs/form/Textarea.vue';
import BaseModal from '@/components/Zcrat/modals/BaseModal.vue';
import type { option } from '@/types/generales';
import type { buttonconfirmed } from '@/types/modals';
import MyBasicToast from '@/utils/ToastNotificationBasic';
import axios from 'axios';
import { computed, reactive, ref, watch } from 'vue';

interface CatalogItem {
    id: number;
    descripcion: string;
    codigo_sat?: string;
    codigo?: string;
    modulo_id?: number;
    modulo?: string;
}

interface Catalogos {
    tipos: CatalogItem[];
    modulos: CatalogItem[];
    categorias_sat: CatalogItem[];
    unidades_sat: CatalogItem[];
    vehiculos: CatalogItem[];
}

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

const props = defineProps<{
    show: boolean;
    costoId: number | null;
}>();

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
const catalogos = ref<Catalogos>({
    tipos: [],
    modulos: [],
    categorias_sat: [],
    unidades_sat: [],
    vehiculos: [],
});
const errors = ref<Record<string, string[]>>({});
const loading = ref(false);
const loadedCatalogs = ref(false);
const selectedCategoria = ref<option | null>(null);
const selectedCategoriaSat = ref<option | null>(null);
const selectedUnidadSat = ref<option | null>(null);
const selectedVehiculo = ref<option | null>(null);

const total = computed(() => Number(form.p_refaccion || 0) + Number(form.p_mano_obra || 0));
const vehiculoParams = computed(() => ({
    modulo_id: form.modulo_id,
}));

const formatCurrency = (value: number) =>
    new Intl.NumberFormat('es-MX', {
        style: 'currency',
        currency: 'MXN',
    }).format(value);

const resetForm = () => {
    Object.assign(form, emptyForm());
    selectedCategoria.value = null;
    selectedCategoriaSat.value = null;
    selectedUnidadSat.value = null;
    selectedVehiculo.value = null;
    errors.value = {};
};

const asOption = (item: CatalogItem | undefined, label: string): option | null =>
    item
        ? {
              value: item.id,
              label,
          }
        : null;

const clearError = (field: string) => {
    delete errors.value[field];
};

watch(selectedCategoria, (value) => {
    form.tipo_id = value ? Number(value.value) : null;
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
        if (!loadedCatalogs.value) {
            const response = await axios.get<Catalogos>(route('catalogos.conceptos.catalogos'));
            catalogos.value = response.data;
            loadedCatalogs.value = true;
        }

        if (props.costoId !== null) {
            const response = await axios.get(route('catalogos.conceptos.show', props.costoId));
            Object.assign(form, {
                numero: response.data.numero,
                descripcion: response.data.descripcion,
                garantia_dias: response.data.garantia_dias,
                tipo_id: response.data.tipo_id,
                modulo_id: response.data.modulo_id,
                categoria_sat_id: response.data.categoria_sat_id,
                unidad_sat_id: response.data.unidad_sat_id,
                vehiculo_id: response.data.vehiculo_id,
                p_refaccion: Number(response.data.p_refaccion),
                p_mano_obra: Number(response.data.p_mano_obra),
            });

            const tipo = catalogos.value.tipos.find((item) => item.id === response.data.tipo_id);
            const categoriaSat = catalogos.value.categorias_sat.find((item) => item.id === response.data.categoria_sat_id);
            const unidadSat = catalogos.value.unidades_sat.find((item) => item.id === response.data.unidad_sat_id);
            const vehiculo = catalogos.value.vehiculos.find(
                (item) => item.id === response.data.vehiculo_id && item.modulo_id === response.data.modulo_id,
            );

            selectedCategoria.value = asOption(tipo, tipo?.descripcion ?? '');
            selectedCategoriaSat.value = asOption(categoriaSat, `${categoriaSat?.descripcion ?? ''} — ${categoriaSat?.codigo_sat ?? ''}`);
            selectedUnidadSat.value = asOption(unidadSat, `${unidadSat?.descripcion ?? ''} — ${unidadSat?.codigo ?? ''}`);
            selectedVehiculo.value = asOption(vehiculo, vehiculo?.descripcion ?? '');
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
        if (props.costoId === null) {
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

const buttonConfirm = computed<buttonconfirmed>(() => ({
    text: props.costoId === null ? 'Crear concepto' : 'Guardar cambios',
    classname: 'bg-blue-700 text-white',
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
        :loading="loading"
        textLoading="Espera a que termine la operación"
        :modaltitle="costoId === null ? 'Nuevo concepto' : 'Modificar concepto'"
        position="center"
        :buttonconfirm="buttonConfirm"
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
                endpoint="catalogos.conceptos.opciones.categorias"
                label="Categoría"
                placeholder="Buscar categoría"
                :cacheoptions="false"
                :errors="errors.tipo_id"
                :DeleteErrors="() => clearError('tipo_id')"
            />

            <label class="flex flex-col">
                Módulo
                <select v-model.number="form.modulo_id" class="rounded-md">
                    <option :value="null" disabled>Selecciona un módulo</option>
                    <option v-for="item in catalogos.modulos" :key="item.id" :value="item.id">
                        {{ item.descripcion }}
                    </option>
                </select>
                <span v-if="fieldError('modulo_id')" class="text-sm text-red-600">{{ fieldError('modulo_id') }}</span>
            </label>

            <ZDRemoteSelect
                v-model="selectedCategoriaSat"
                endpoint="catalogos.conceptos.opciones.categorias-sat"
                label="Categoría SAT"
                placeholder="Descripción o código SAT"
                :cacheoptions="false"
                :errors="errors.categoria_sat_id"
                :DeleteErrors="() => clearError('categoria_sat_id')"
            />

            <ZDRemoteSelect
                v-model="selectedUnidadSat"
                endpoint="catalogos.conceptos.opciones.unidades-sat"
                label="Unidad SAT"
                placeholder="Descripción o código"
                :cacheoptions="false"
                :errors="errors.unidad_sat_id"
                :DeleteErrors="() => clearError('unidad_sat_id')"
            />

            <ZDRemoteSelect
                v-model="selectedVehiculo"
                endpoint="catalogos.conceptos.opciones.vehiculos"
                label="Vehículo"
                placeholder="Buscar vehículo"
                class="sm:col-span-2"
                :params="vehiculoParams"
                :disabled="form.modulo_id === null"
                :cacheoptions="false"
                :errors="errors.vehiculo_id"
                :DeleteErrors="() => clearError('vehiculo_id')"
            />

            <InputBasic id="precio_refaccion" v-model="form.p_refaccion" type="number" label="Precio refacción" :errors="errors.p_refaccion" />

            <InputBasic id="precio_mano_obra" v-model="form.p_mano_obra" type="number" label="Precio mano de obra" :errors="errors.p_mano_obra" />
        </div>
    </BaseModal>
</template>
