<script setup lang="ts">
import ConceptosPresupuesto from '@/components/Zcrat/Conceptos/ConceptosPresupuesto.vue';
import Select from '@/components/Zcrat/Elements/Select.vue';
import Subtitle from '@/components/Zcrat/Elements/Subtitle.vue';
import ZDDataPicker from '@/components/Zcrat/Elements/ZDDataPicker.vue';
import ZDRemoteSelect from '@/components/Zcrat/Elements/ZDRemoteSelect.vue';
import Combobox from '@/components/Zcrat/Elements/ZdCombobox.vue';
import VehiculoFields from '@/components/Zcrat/Forms/VehiculoFields.vue';
import InputBasic from '@/components/Zcrat/Inputs/form/InputBasic.vue';
import Textarea from '@/components/Zcrat/Inputs/form/Textarea.vue';
import BaseModal from '@/components/Zcrat/modals/BaseModal.vue';
import { mapPresupuestoResponse } from '@/helpers/mapPresupuestoResponse';
import Create from '@/services/presupuesto/create';
import { getDatosPresupuesto } from '@/services/presupuestoService';
import type {
    ConceptoPresupuestoAsignado,
    NuevoPresupuesto,
    option,
    VehiculoForm,
} from '@/types/generales';
import type { buttonconfirmed } from '@/types/modals';
import MyBasicToast from '@/utils/ToastNotificationBasic';
import { sumarDiasSinDomingo } from '@/utils/functions/generales/fechas';
import GetModulosDisponibles from '@/utils/functions/select/ModulosCortana';
import GetNivelesGasolina from '@/utils/functions/select/NivelesGasolina';
import GetTiposPorCategoria from '@/utils/functions/select/TiposPorCategoria';
import axios from 'axios';
import {
    computed,
    onBeforeUnmount,
    onMounted,
    reactive,
    ref,
    watch,
} from 'vue';

defineOptions({ name: 'PresupuestoModal' });

interface PresupuestoResponse {
    presupuesto: Omit<NuevoPresupuesto, 'estimacion' | 'vigencia'> & {
        id: number;
        estimacion: string | null;
        vigencia: string | null;
    };
    orden_servicio: option | null;
    empresa: option | null;
    cliente: option | null;
    vehiculo_concepto: option | null;
    modulo: option | null;
    vehiculo: VehiculoForm;
    conceptos: ConceptoPresupuestoAsignado[];
}

const props = defineProps<{
    show: boolean;
    presupuestoId?: number | null;
}>();

const emit = defineEmits<{
    (event: 'close'): void;
    (event: 'saved'): void;
}>();

const isEditing = computed(() => props.presupuestoId !== null && props.presupuestoId !== undefined);
const loading = ref(false);
const saving = ref(false);
const savingConcepts = ref(false);
const tipos = ref<option[]>([]);
const nivelesGasolina = ref<option[]>([]);
const modulos = ref<option[]>([]);
const ordenServicio = ref<option | null>(null);
const empresa = ref<option | null>(null);
const cliente = ref<option | null>(null);
const vehiculoConcepto = ref<option | null>(null);
const moduloActual = ref<option | null>(null);
const validationErrors = ref<Record<string, string[]>>({});
const conceptos = ref<ConceptoPresupuestoAsignado[]>([]);
const initialPresupuesto = (): NuevoPresupuesto => ({
    orden_servicio: '',
    folio: '',
    orden_seguimiento: '',
    ubicacion: '',
    telefono: null,
    empresa_id: null,
    cliente_id: null,
    gasolina: '',
    kilometraje: null,
    estimacion: sumarDiasSinDomingo(new Date(new Date().setHours(12, 0)), 2),
    administrador: '',
    jefe: '',
    trabajador: '',
    tecnico: '',
    descripcion_mo: '',
    indicaciones_cliente: '',
    garantia: 'LO ESTIPULADO EN EL CONTRATO',
    observaciones: 'DE ACUERDO A LO DIFICIL DE LA FALLA PARA SU REPARACION',
    tipo_id: 7,
    vehiculo_concepto_id: null,
    economico: '',
    placas: '',
    vin: '',
    color: '',
    vehiculo_tipo_id: null,
    marca_id: null,
    motor_id: null,
    marca: '',
    modelo: '',
    motor: '',
    año: null,
    vigencia: null,
    modulo_orden: '',
});
const initialVehicle = (): VehiculoForm => ({
    placas: '',
    economico: '',
    vin: '',
    año: '',
    tipo_id: null,
    color: '',
    modelo: null,
    marca: null,
    motor: null,
});
const presupuesto = reactive<NuevoPresupuesto>(initialPresupuesto());
const vehiculo = reactive<VehiculoForm>(initialVehicle());
const conceptContext = computed(() => ({
    modulo: moduloActual.value,
    vehiculo: vehiculoConcepto.value,
}));

const loadCatalogs = async () => {
    const [fuelOptions, moduleOptions, typeOptions] = await Promise.all([
        GetNivelesGasolina(),
        GetModulosDisponibles(),
        GetTiposPorCategoria(2),
    ]);
    nivelesGasolina.value = fuelOptions ?? [];
    modulos.value = moduleOptions ?? [];
    tipos.value = typeOptions;
    if (typeOptions.length === 0) {
        MyBasicToast.error('No fue posible cargar los tipos de presupuesto');
    }
    if (
        moduloActual.value &&
        !modulos.value.some(
            (item) => Number(item.value) === Number(moduloActual.value?.value),
        )
    ) {
        modulos.value.push(moduloActual.value);
    }
};

const resetCreateForm = () => {
    Object.assign(presupuesto, initialPresupuesto());
    Object.assign(vehiculo, initialVehicle());
    ordenServicio.value = null;
    empresa.value = null;
    cliente.value = null;
    vehiculoConcepto.value = null;
    moduloActual.value = null;
    validationErrors.value = {};
    conceptos.value = [];
};

let orderDataController: AbortController | null = null;
const loadOrderData = async (order: string) => {
    orderDataController?.abort();
    if (isEditing.value || !order) return;

    orderDataController = new AbortController();
    const result = await getDatosPresupuesto(
        order,
        orderDataController.signal,
    );

    if (!result.status || !result.data) return;

    mapPresupuestoResponse(
        result.data,
        presupuesto,
        empresa,
        cliente,
        vehiculoConcepto,
    );
    if (!(presupuesto.estimacion instanceof Date)) {
        presupuesto.estimacion = new Date(presupuesto.estimacion);
    }
    Object.assign(vehiculo, {
        ...result.data.vehiculo,
        tipo_id: result.data.vehiculo.tipo_id === null
            ? null
            : Number(result.data.vehiculo.tipo_id),
    });
};

const load = async () => {
    if (!isEditing.value || !props.presupuestoId) return;
    loading.value = true;
    validationErrors.value = {};
    vehiculo.error = undefined;
    conceptos.value = [];

    try {
        const response = await axios.get<PresupuestoResponse>(route('presupuesto.show', props.presupuestoId));
        const data = response.data;
        Object.assign(presupuesto, {
            ...data.presupuesto,
            estimacion: data.presupuesto.estimacion
                ? new Date(data.presupuesto.estimacion)
                : presupuesto.estimacion,
            vigencia: data.presupuesto.vigencia
                ? new Date(`${data.presupuesto.vigencia}T12:00:00`)
                : null,
        });
        Object.assign(vehiculo, {
            ...data.vehiculo,
            tipo_id: data.vehiculo.tipo_id === null
                ? null
                : Number(data.vehiculo.tipo_id),
        });
        ordenServicio.value = data.orden_servicio;
        empresa.value = data.empresa;
        cliente.value = data.cliente;
        vehiculoConcepto.value = data.vehiculo_concepto;
        moduloActual.value = data.modulo;

        if (
            data.modulo &&
            !modulos.value.some((item) => Number(item.value) === Number(data.modulo?.value))
        ) {
            modulos.value.push(data.modulo);
        }

        conceptos.value = data.conceptos;
    } catch {
        MyBasicToast.error('No fue posible obtener el presupuesto');
        emit('close');
    } finally {
        loading.value = false;
    }
};

const save = async () => {
    if (!isEditing.value) {
        saving.value = true;
        validationErrors.value = {};
        vehiculo.error = undefined;

        try {
            const response = await Create(presupuesto);
            if (response.status) {
                MyBasicToast.success(response.data.message);
                emit('saved');
                emit('close');
                return;
            }

            if (response.code === 422 && response.validationErrors) {
                validationErrors.value = response.validationErrors ?? {};
                vehiculo.error = response.validationErrors;
                MyBasicToast.error('Revisa los datos del presupuesto');
            } else {
                MyBasicToast.error(response.message);
            }
        } finally {
            saving.value = false;
        }
        return;
    }

    if (!props.presupuestoId) return;
    saving.value = true;
    validationErrors.value = {};
    vehiculo.error = undefined;

    try {
        const response = await axios.put(
            route('presupuesto.update', props.presupuestoId),
            { ...presupuesto },
        );
        MyBasicToast.success(response.data.message);
        emit('saved');
        emit('close');
    } catch (error) {
        if (axios.isAxiosError(error) && error.response?.status === 422) {
            validationErrors.value = error.response.data.errors ?? {};
            vehiculo.error = error.response.data.errors;
            MyBasicToast.error('Revisa los datos del presupuesto');
        } else {
            MyBasicToast.error('No fue posible actualizar el presupuesto');
        }
    } finally {
        saving.value = false;
    }
};

const buttonConfirm = computed<buttonconfirmed>(() => ({
    text: saving.value
        ? 'Guardando...'
        : isEditing.value
            ? 'Guardar cambios'
            : 'Crear presupuesto',
    classname: 'bg-blue-700 text-white',
    disabled: saving.value
        || loading.value
        || (
            !isEditing.value
            && Object.entries(presupuesto)
                .filter(([key]) => ![
                    'orden_servicio',
                    'folio',
                    'orden_seguimiento',
                    'vigencia',
                ].includes(key))
                .some(([, value]) => value === null || value === '')
        ),
    onClick: save,
}));

watch(
    () => [props.show, props.presupuestoId],
    ([show]) => {
        if (!show) {
            orderDataController?.abort();
            return;
        }
        if (isEditing.value) {
            load();
        } else {
            resetCreateForm();
        }
    },
    { immediate: true },
);

watch(ordenServicio, (currentOrder) => {
    presupuesto.orden_servicio = currentOrder
        ? String(currentOrder.value)
        : '';
});

watch(() => presupuesto.orden_servicio, (currentOrder) => {
    loadOrderData(currentOrder);
});

watch(vehiculo, (currentVehicle) => {
    Object.assign(presupuesto, {
        economico: currentVehicle.economico,
        placas: currentVehicle.placas,
        vin: currentVehicle.vin,
        año: currentVehicle.año === '' ? null : Number(currentVehicle.año),
        color: currentVehicle.color,
        vehiculo_tipo_id: currentVehicle.tipo_id,
        marca_id: currentVehicle.marca?.value ?? null,
        marca: currentVehicle.marca?.label ?? '',
        modelo: currentVehicle.modelo?.label ?? '',
        motor_id: currentVehicle.motor?.value ?? null,
        motor: currentVehicle.motor?.label ?? '',
    });
}, { deep: true, immediate: true });

watch(empresa, (currentEmpresa) => {
    presupuesto.empresa_id = currentEmpresa
        ? Number(currentEmpresa.value)
        : null;
});

watch(cliente, (currentClient) => {
    presupuesto.cliente_id = currentClient
        ? Number(currentClient.value)
        : null;
});

watch(vehiculoConcepto, (currentVehicleConcept) => {
    presupuesto.vehiculo_concepto_id = currentVehicleConcept
        ? Number(currentVehicleConcept.value)
        : null;
});

onMounted(loadCatalogs);
onBeforeUnmount(() => orderDataController?.abort());
</script>

<template>
    <BaseModal
        :show="show"
        :modaltitle="isEditing ? 'Modificar presupuesto' : 'Nuevo presupuesto'"
        :modaldescription="isEditing
            ? 'Actualiza el presupuesto y administra sus conceptos'
            : 'Captura los datos del nuevo presupuesto'"
        position="center"
        :loading="loading || saving || savingConcepts"
        :buttonconfirm="buttonConfirm"
        @close="emit('close')"
    >
        <div class="flex w-[min(72rem,calc(100vw-3rem))] flex-col gap-4 pb-2">
            <Subtitle>Datos Generales</Subtitle>
            <div class="grid grid-cols-1 gap-2 sm:grid-cols-2 md:grid-cols-3">
                <ZDRemoteSelect
                    v-model="ordenServicio"
                    endpoint="select2.presupuesto.ordenes-servicio"
                    label="ORDEN DE SERVICIO"
                    placeholder="Orden existente"
                    classDiv="w-full"
                    :clearable="false"
                    :disabled="isEditing"
                    :errors="validationErrors.orden_servicio"
                />
                <InputBasic
                    id="folio"
                    v-model="presupuesto.folio"
                    label="FOLIO"
                    type="text"
                    placeholder="Automático o ingresar"
                    :errors="validationErrors.folio"
                />
                <InputBasic
                    id="ordenseguimiento"
                    v-model="presupuesto.orden_seguimiento"
                    label="ORDEN DE SEGUIMIENTO"
                    type="text"
                    placeholder="Automático o ingresar"
                    :errors="validationErrors.orden_seguimiento"
                />
                <Combobox
                    id="ubicacion"
                    v-model="presupuesto.ubicacion"
                    label="UBICACIÓN"
                    endpoint="combobox.ubicaciones.lista"
                    placeholder="Buscar o crear"
                    :errors="validationErrors.ubicacion"
                />
                <InputBasic
                    id="telefono"
                    v-model="presupuesto.telefono"
                    label="TELÉFONO"
                    type="text"
                    placeholder="Teléfono de contacto"
                    :errors="validationErrors.telefono"
                />
                <ZDDataPicker
                    v-model="presupuesto.estimacion"
                    label="FECHA ESTIMADA"
                    :clearable="false"
                    :time="true"
                    :range="false"
                    class="w-full"
                    :disabled="isEditing"
                    :errors="validationErrors.estimacion"
                />
                <ZDDataPicker
                    v-if="isEditing"
                    v-model="presupuesto.vigencia"
                    label="VIGENCIA"
                    :clearable="true"
                    :time="false"
                    :range="false"
                    class="w-full"
                    :errors="validationErrors.vigencia"
                />
                <InputBasic
                    id="kilometraje"
                    v-model="presupuesto.kilometraje"
                    label="KILOMETRAJE"
                    type="number"
                    placeholder="ej. 392"
                    :errors="validationErrors.kilometraje"
                />
                <Select
                    id="gasolina"
                    v-model="presupuesto.gasolina"
                    label="GASOLINA"
                    :canempty="true"
                    :options="nivelesGasolina"
                    :errors="validationErrors.gasolina"
                />
                <Select
                    id="presupuestotipo"
                    v-model="presupuesto.tipo_id"
                    label="TIPO DE PRESUPUESTO"
                    :options="tipos"
                    :disabled="isEditing"
                    :errors="validationErrors.tipo_id"
                />
                <ZDRemoteSelect
                    v-model="vehiculoConcepto"
                    endpoint="select2.vehiculos.conceptos.modulos"
                    :params="{ id_modulo: presupuesto.modulo_orden }"
                    label="VEHÍCULO DE LOS CONCEPTOS"
                    :empty_message="presupuesto.modulo_orden
                        ? 'Sin resultados'
                        : 'Selecciona un módulo'"
                    placeholder="Seleccionar vehículo"
                    classDiv="w-full"
                    :errors="validationErrors.vehiculo_concepto_id"
                />
                <ZDRemoteSelect
                    v-model="empresa"
                    endpoint="select2.empresas"
                    label="EMPRESA"
                    placeholder="Buscar empresa"
                    classDiv="w-full"
                    :errors="validationErrors.empresa_id"
                />
                <ZDRemoteSelect
                    v-model="cliente"
                    endpoint="select2.clientes"
                    :params="{ empresa_id: empresa?.value }"
                    label="CLIENTE"
                    :empty_message="empresa
                        ? 'Sin resultados'
                        : 'Selecciona una empresa'"
                    placeholder="Buscar cliente"
                    classDiv="w-full"
                    :disabled="empresa === null"
                    :errors="validationErrors.cliente_id"
                />
                <Select
                    id="moduloorden"
                    v-model="presupuesto.modulo_orden"
                    label="MÓDULO ORDEN"
                    :canempty="true"
                    :options="modulos"
                    :disabled="isEditing"
                    :errors="validationErrors.modulo_orden"
                />
            </div>

            <Subtitle>Empleados Encargados</Subtitle>
            <div class="grid grid-cols-1 gap-2 md:grid-cols-2">
                <Combobox
                    id="administradortrasporte"
                    v-model="presupuesto.administrador"
                    endpoint="combobox.administradores_trasporte"
                    label="ADMINISTRADOR DE TRANSPORTES"
                    placeholder="Buscar o crear"
                    :errors="validationErrors.administrador"
                />
                <Combobox
                    id="jefeproceso"
                    v-model="presupuesto.jefe"
                    endpoint="combobox.jefes_procesos"
                    label="JEFE DE PROCESOS"
                    placeholder="Buscar o crear"
                    :errors="validationErrors.jefe"
                />
                <Combobox
                    id="trabajador"
                    v-model="presupuesto.trabajador"
                    endpoint="combobox.trabajadores"
                    label="TRABAJADOR"
                    placeholder="Buscar o crear"
                    :errors="validationErrors.trabajador"
                />
                <Combobox
                    id="tecnico"
                    v-model="presupuesto.tecnico"
                    endpoint="combobox.tecnicos"
                    label="TÉCNICO"
                    placeholder="Buscar o crear"
                    :errors="validationErrors.tecnico"
                />
            </div>

            <Subtitle>Notas</Subtitle>
            <div class="grid grid-cols-1 gap-2 md:grid-cols-2">
                <Textarea
                    id="indicacionescliente"
                    v-model="presupuesto.indicaciones_cliente"
                    label="INDICACIONES DEL CLIENTE"
                    placeholder="Escribe las indicaciones del cliente"
                    classname="h-24"
                    :errors="validationErrors.indicaciones_cliente"
                />
                <Textarea
                    id="notasmecanico"
                    v-model="presupuesto.descripcion_mo"
                    label="DESCRIPCIÓN MANO DE OBRA"
                    placeholder="Escribe las notas del mecánico"
                    classname="h-24"
                    :errors="validationErrors.descripcion_mo"
                />
                <Textarea
                    id="observaciones"
                    v-model="presupuesto.garantia"
                    label="GARANTÍA"
                    placeholder="Escribe la garantía"
                    classname="h-24"
                    :errors="validationErrors.garantia"
                />
                <Textarea
                    id="descripcionmo"
                    v-model="presupuesto.observaciones"
                    label="TIEMPO DE ENTREGA"
                    placeholder="Escribe el tiempo de entrega"
                    classname="h-24"
                    :errors="validationErrors.observaciones"
                />
            </div>

            <Subtitle>Datos Vehículo</Subtitle>
            <VehiculoFields
                v-model:economico="vehiculo.economico"
                v-model:placas="vehiculo.placas"
                v-model:vin="vehiculo.vin"
                v-model:anio="vehiculo.año"
                v-model:color="vehiculo.color"
                v-model:tipoId="vehiculo.tipo_id"
                v-model:marca="vehiculo.marca"
                v-model:modelo="vehiculo.modelo"
                v-model:motor="vehiculo.motor"
                :uppercaseLabels="true"
                :errors="vehiculo.error"
            />

            <ConceptosPresupuesto
                v-if="isEditing && presupuestoId"
                :presupuesto-id="presupuestoId"
                :conceptos="conceptos"
                :modulo="conceptContext.modulo"
                :vehiculo="conceptContext.vehiculo"
                @loading="savingConcepts = $event"
                @reload="load"
                @saved="emit('saved')"
            />
        </div>
    </BaseModal>
</template>
