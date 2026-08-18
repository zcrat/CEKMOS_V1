<script setup lang="ts">
import Button from '@/components/Zcrat/Inputs/Button.vue';
import MyBasicToast from '@/utils/ToastNotificationBasic';
import axios from 'axios';
import { computed, ref, watch } from 'vue';

interface OrdenValeAlmacen {
    id: number;
    numero: string;
    economico: string;
    placas: string;
    motor: string;
}

interface ConceptoForm {
    clave: string;
    cantidad: number;
    descripcion: string;
}

interface ValeAlmacenGuardado {
    id: number;
    status: number;
    tipo: number;
    tipo_descripcion: string;
    siguiente_estatus: string | null;
    folio: string;
    destino: string;
    motor: string;
    fecha: string;
    estatus: string;
    pdf_url: string;
    conceptos: ConceptoForm[];
}

const props = withDefaults(
    defineProps<{
        orden: OrdenValeAlmacen;
        vale?: ValeAlmacenGuardado | null;
    }>(),
    {
        vale: null,
    },
);

const saving = defineModel<boolean>('saving', { default: false });
const emit = defineEmits<{
    (event: 'cancel'): void;
    (event: 'saved', vale: ValeAlmacenGuardado): void;
}>();

const errors = ref<Record<string, string[]>>({});
const form = ref(createForm());
const isEditing = computed(() => Boolean(props.vale));
const title = computed(() => (isEditing.value ? `Editar vale ${props.vale?.folio}` : 'Nuevo vale de almacén'));

function createForm() {
    return {
        destino: props.vale?.destino ?? 'ALMACÉN',
        motor: props.vale?.motor ?? props.orden.motor,
        conceptos: props.vale?.conceptos.map((concepto) => ({ ...concepto })) ?? [{ clave: '', cantidad: 1, descripcion: '' }],
    };
}

watch(
    [() => props.orden, () => props.vale],
    () => {
        form.value = createForm();
        errors.value = {};
    },
    { deep: true },
);

const addConcept = () => {
    form.value.conceptos.push({ clave: '', cantidad: 1, descripcion: '' });
};

const removeConcept = (index: number) => {
    if (form.value.conceptos.length > 1) form.value.conceptos.splice(index, 1);
};

const fieldError = (key: string) => errors.value[key]?.[0] ?? '';

const save = async () => {
    saving.value = true;
    errors.value = {};

    try {
        const { data } =
            isEditing.value && props.vale
                ? await axios.put(route('vales-almacen.update', props.vale.id), form.value)
                : await axios.post(route('vales-almacen.store', props.orden.id), form.value);
        MyBasicToast.success(data.message);
        emit('saved', data.vale);
    } catch (error) {
        if (axios.isAxiosError(error) && error.response?.status === 422) {
            errors.value = error.response.data.errors ?? {};
            MyBasicToast.warning('Revisa los datos del vale.');
        } else {
            MyBasicToast.error(
                axios.isAxiosError(error)
                    ? (error.response?.data?.message ?? `No fue posible ${isEditing.value ? 'actualizar' : 'crear'} el vale de almacén.`)
                    : `No fue posible ${isEditing.value ? 'actualizar' : 'crear'} el vale de almacén.`,
            );
        }
    } finally {
        saving.value = false;
    }
};
</script>

<template>
    <form class="h-[calc(90vh-11rem)] min-h-[420px] overflow-auto bg-white px-4 py-4" @submit.prevent="save">
        <div class="mx-auto w-full max-w-6xl space-y-4">
            <div class="flex items-center justify-between border-b pb-2">
                <h3 class="text-lg font-semibold">{{ title }}</h3>
            </div>

            <div class="rounded-lg bg-slate-50 p-3 text-sm text-slate-700">
                <strong>Orden {{ orden.numero }}</strong> · Económico {{ orden.economico || '-' }} · Placas {{ orden.placas || '-' }}
            </div>

            <div class="grid gap-3 md:grid-cols-2">
                <label class="text-sm font-semibold">
                    Destino
                    <input v-model="form.destino" maxlength="100" class="mt-1 w-full rounded-md border-gray-300 uppercase" />
                    <span v-if="fieldError('destino')" class="block text-xs text-red-600">{{ fieldError('destino') }}</span>
                </label>
                <label class="text-sm font-semibold">
                    Motor
                    <input v-model="form.motor" maxlength="100" class="mt-1 w-full rounded-md border-gray-300 uppercase" />
                    <span v-if="fieldError('motor')" class="block text-xs text-red-600">{{ fieldError('motor') }}</span>
                </label>
            </div>

            <div>
                <div class="mb-2 flex items-center justify-between">
                    <h3 class="font-semibold">Conceptos</h3>
                    <Button type="secondary" size="compact" text="Agregar concepto" icon="fa-solid fa-plus" @click.prevent="addConcept" />
                </div>

                <div class="space-y-2 overflow-x-auto pb-1">
                    <div
                        v-for="(concepto, index) in form.conceptos"
                        :key="index"
                        class="flex min-w-[720px] flex-row items-start gap-2 rounded-md border border-gray-200 p-2"
                    >
                        <div class="w-40 flex-none">
                            <label class="mb-1 block text-xs font-semibold text-gray-600">Clave</label>
                            <input v-model="concepto.clave" maxlength="50" placeholder="Clave" class="h-11 w-full rounded-md border-gray-300 uppercase" />
                            <span v-if="fieldError(`conceptos.${index}.clave`)" class="text-xs text-red-600">
                                {{ fieldError(`conceptos.${index}.clave`) }}
                            </span>
                        </div>
                        <div class="w-32 flex-none">
                            <label class="mb-1 block text-xs font-semibold text-gray-600">Cantidad</label>
                            <input
                                v-model.number="concepto.cantidad"
                                type="number"
                                min="0.01"
                                max="999999.99"
                                step="0.01"
                                placeholder="Cantidad"
                                class="h-11 w-full rounded-md border-gray-300"
                            />
                            <span v-if="fieldError(`conceptos.${index}.cantidad`)" class="text-xs text-red-600">
                                {{ fieldError(`conceptos.${index}.cantidad`) }}
                            </span>
                        </div>
                        <div class="min-w-0 flex-1">
                            <label class="mb-1 block text-xs font-semibold text-gray-600">Descripción</label>
                            <textarea
                                v-model="concepto.descripcion"
                                rows="1"
                                placeholder="Descripción"
                                class="min-h-11 w-full resize-y rounded-md border-gray-300 uppercase"
                            ></textarea>
                            <span v-if="fieldError(`conceptos.${index}.descripcion`)" class="text-xs text-red-600">
                                {{ fieldError(`conceptos.${index}.descripcion`) }}
                            </span>
                        </div>
                        <button
                            type="button"
                            class="mt-[21px] h-11 w-11 flex-none rounded-md border border-red-300 bg-red-50 text-red-700 hover:bg-red-100 disabled:opacity-40"
                            :disabled="form.conceptos.length === 1"
                            title="Quitar concepto"
                            @click="removeConcept(index)"
                        >
                            <font-awesome-icon icon="fa-solid fa-trash" />
                        </button>
                    </div>
                </div>
            </div>

            <div class="flex justify-end gap-2 border-t pt-3">
                <Button type="secondary" text="Cancelar" :disabled="saving" @click.prevent="emit('cancel')" />
                <Button
                    type="save"
                    :text="isEditing ? 'Guardar cambios' : 'Guardar y generar PDF'"
                    icon="fa-solid fa-floppy-disk"
                    :disabled="saving"
                />
            </div>
        </div>
    </form>
</template>
