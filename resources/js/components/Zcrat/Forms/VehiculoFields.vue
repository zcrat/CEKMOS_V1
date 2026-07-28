<script setup lang="ts">
import ZDRemoteSelect from '@/components/Zcrat/Elements/ZDRemoteSelect.vue';
import BaseModal from '@/components/Zcrat/modals/BaseModal.vue';
import InputBasic from '@/components/Zcrat/Inputs/form/InputBasic.vue';
import type { option, VehiculoForm } from '@/types/generales';
import type { buttonconfirmed } from '@/types/modals';
import axios from 'axios';
import { computed, ref, watch } from 'vue';
import TiposVehiculos from './TiposVehiculos.vue';

type CatalogType = 'marca' | 'motor' | 'modelo';

const props = withDefaults(defineProps<{
    errors?: VehiculoForm['error'];
    disabled?: boolean;
    uppercaseLabels?: boolean;
}>(), {
    disabled: false,
    uppercaseLabels: false,
});

const economico = defineModel<string>('economico', { required: true });
const placas = defineModel<string>('placas', { required: true });
const vin = defineModel<string>('vin', { required: true });
const anio = defineModel<string>('anio', { required: true });
const color = defineModel<string>('color', { required: true });
const tipoId = defineModel<number | null>('tipoId', { required: true });
const marca = defineModel<option | null>('marca', { required: true });
const modelo = defineModel<option | null>('modelo', { required: true });
const motor = defineModel<option | null>('motor', { required: true });

const hasMarca = computed(() => marca.value !== null);
const hasModelo = computed(() => modelo.value !== null);
const catalogType = ref<CatalogType>('marca');
const catalogShow = ref(false);
const catalogDescription = ref('');
const catalogErrors = ref<string[]>();
const catalogLoading = ref(false);

const labels = computed(() => {
    const values = {
        economico: 'Económico',
        placas: 'Placas',
        vin: 'VIN',
        anio: 'Año',
        color: 'Color',
        marca: 'Marca',
        modelo: 'Modelo',
        motor: 'Motor',
        tipo: 'Tipo de vehículo',
    };

    if (!props.uppercaseLabels) return values;

    return Object.fromEntries(
        Object.entries(values).map(([key, value]) => [
            key,
            value.toLocaleUpperCase('es-MX'),
        ]),
    ) as typeof values;
});

const catalogTitle = computed(() => (
    catalogType.value === 'marca'
        ? 'Nueva marca'
        : `Nuevo ${catalogType.value}`
));

watch(
    () => marca.value?.value,
    (currentMarca, previousMarca) => {
        if (previousMarca !== undefined && currentMarca !== previousMarca) {
            modelo.value = null;
            motor.value = null;
        }
    },
    { flush: 'sync' },
);

watch(
    () => modelo.value?.value,
    (currentModelo, previousModelo) => {
        if (previousModelo !== undefined && currentModelo !== previousModelo) {
            motor.value = null;
        }
    },
    { flush: 'sync' },
);

const openCatalog = (type: CatalogType) => {
    catalogType.value = type;
    catalogDescription.value = '';
    catalogErrors.value = undefined;
    catalogShow.value = true;
};

const saveCatalog = async () => {
    if (catalogType.value === 'modelo') {
        const description = catalogDescription.value
            .trim()
            .toLocaleLowerCase('es-MX');
        modelo.value = { value: description, label: description };
        catalogShow.value = false;
        return;
    }

    try {
        catalogLoading.value = true;
        catalogErrors.value = undefined;
        const response = await axios.post(route('vehiculo.catalogo.create'), {
            tipo: catalogType.value,
            descripcion: catalogDescription.value,
        });
        const catalogOption: option = response.data.option;

        if (catalogType.value === 'marca') marca.value = catalogOption;
        if (catalogType.value === 'motor') motor.value = catalogOption;
        catalogShow.value = false;
    } catch (error: any) {
        catalogErrors.value = error.response?.data?.errors?.descripcion
            ?? [error.response?.data?.message ?? 'No fue posible guardar el catálogo'];
    } finally {
        catalogLoading.value = false;
    }
};

const catalogButtonConfirm = computed<buttonconfirmed>(() => ({
    text: 'Guardar',
    classname: 'bg-blue-600 text-white',
    onClick: saveCatalog,
    disabled: catalogDescription.value.trim() === '' || catalogLoading.value,
}));
</script>

<template>
    <div class="grid grid-cols-1 gap-2 sm:grid-cols-2 lg:grid-cols-4">
        <InputBasic
            id="Economico"
            v-model="economico"
            :label="labels.economico"
            type="text"
            placeholder="ej. 254335"
            :disabled="disabled"
            :errors="errors?.economico"
        />
        <InputBasic
            id="Placas"
            v-model="placas"
            :label="labels.placas"
            type="text"
            placeholder="ej. PHU234T"
            :disabled="disabled"
            :errors="errors?.placas"
        />
        <InputBasic
            id="Vin"
            v-model="vin"
            :label="labels.vin"
            type="text"
            placeholder="Ej. JJSOE18P388988750"
            :disabled="disabled"
            :errors="errors?.vin"
        />
        <InputBasic
            id="Año"
            v-model="anio"
            :label="labels.anio"
            type="number"
            placeholder="ej. 2024"
            :disabled="disabled"
            :errors="errors?.año"
        />
        <InputBasic
            id="Color"
            v-model="color"
            :label="labels.color"
            type="text"
            classname="uppercase"
            placeholder="ej. ROJO"
            :disabled="disabled"
            :errors="errors?.color"
        />
        <ZDRemoteSelect
            v-model="marca"
            endpoint="select2.catalogo.marcas"
            :label="labels.marca"
            :buttonNew="() => openCatalog('marca')"
            :canNew="true"
            :cacheoptions="false"
            placeholder="Seleccionar marca"
            :disabled="disabled"
            :errors="errors?.marca_id"
        />
        <ZDRemoteSelect
            v-model="modelo"
            endpoint="select2.catalogo.modelos"
            :label="labels.modelo"
            :buttonNew="() => openCatalog('modelo')"
            :canNew="true"
            :cacheoptions="false"
            :disabled="disabled || !hasMarca"
            :params="{ marca_id: marca?.value }"
            placeholder="Seleccionar modelo"
            :errors="errors?.modelo"
        />
        <ZDRemoteSelect
            v-model="motor"
            endpoint="select2.catalogo.motores"
            :label="labels.motor"
            :canNew="true"
            :searchable="false"
            :cacheoptions="false"
            :disabled="disabled || !hasModelo"
            placeholder="Seleccionar motor"
            :errors="errors?.motor_id"
        />
        <TiposVehiculos
            v-model="tipoId"
            :label="labels.tipo"
            :disabled="disabled"
            :errors="errors?.vehiculo_tipo_id ?? errors?.tipo_id"
        />
    </div>

    <BaseModal
        :show="catalogShow"
        :modaltitle="catalogTitle"
        position="center"
        z="z-[999]"
        :loading="catalogLoading"
        :buttonconfirm="catalogButtonConfirm"
        @close="catalogShow = false"
    >
        <div class="w-80 pb-2">
            <InputBasic
                id="CatalogDescription"
                v-model="catalogDescription"
                :label="uppercaseLabels ? 'DESCRIPCIÓN' : 'Descripción'"
                type="text"
                placeholder="Descripción del nuevo registro"
                :errors="catalogErrors"
            />
        </div>
    </BaseModal>
</template>
