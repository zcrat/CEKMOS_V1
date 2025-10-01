<!-- ModalExample.vue -->
<script setup lang="ts">

import InputBasic from '../Inputs/form/InputBasic.vue'
import BaseModal from '@/components/Zcrat/modals/BasicModal.vue'
import ButtonTogglePYR from '@/components/Zcrat/Inputs/ButtonTogglePYR.vue'
import MyBasicToast from '@/utils/ToastNotificationBasic'
import axios from 'axios'
import { ref, watch ,reactive, onMounted} from 'vue' 
import Loanding from '@/components/Zcrat/Elements/Loanding.vue';
import { type DatosPresupuestos,type DatosOrdenServicio,type DatosEntrada, option } from '@/utils/interfaces/generales'
import Combobox from '@/components/Zcrat/Elements/ZdCombobox.vue'
import Datapicker from '@/components/Zcrat/Elements/ZDDataPicker.vue';
import Select from '@/components/Zcrat/Elements/Select.vue';

const props = defineProps<{show: boolean}>()
const emit = defineEmits(['update:show'])
const options=ref<option[]>([])
const updateVisibility = (val: boolean) => {
  emit('update:show', val)
}

onMounted(() => {
  GetNivelesGasolina();
});

const GetNivelesGasolina = async () => {
  try {
    const response = await axios.get(route('select.niveles.combustible'))
    options.value = response.data.options
  } catch (error: any) {
    console.error(error)
  } 
}
const presupuesto = reactive<DatosPresupuestos>({ // Use reactive to make newEmployee reactive
  observaciones: '',
  descripcion_mo: '',
  garantia: '',
  folio: '',
  vigencia: null,
  factura_id: null,
  tipo_id:null,
  estatus_id: null,
})
const datosentrada = reactive<DatosEntrada>({ // Use reactive to make newEmployee reactive
  estimacion: sumarDiasSinDomingo(new Date(new Date().setHours(12,0)),2),
  kilometraje: null,
  gasolina: "",
})
const ordenservicio = reactive<DatosOrdenServicio>({ // Use reactive to make newEmployee reactive
  orden_servicio: '',
  orden_seguimiento: '',
  modulo_orden_id: null,
  vehiculo_id: null,
  vehiculo_concepto_id: null,
  user_id: null,
  empresa_id: null,
  cliente_id: null,
  update_fotos: null,
  diagnostico: null,
  indicaciones_cliente: '',
  notas_mecanico: '',
  notas_retraso: '',
  telefono: '',
  ubicacion: '',
})

function sumarDiasSinDomingo(fecha: Date, dias: number): Date {
  const resultado = new Date(fecha);
  let diasSumados = 0;

  while (diasSumados < dias) {
    resultado.setDate(resultado.getDate() + 1);
    if (resultado.getDay() !== 0) {
      diasSumados++;
    }
  }

  return resultado;
}

</script>

<template>
  <BaseModal modaltitle="Presupuestos" :position="'center'" :modelValue="props.show" @update:modelValue="updateVisibility" >
    <div class="grid sm:grid-cols-2 md:grid-cols-4 gap-2" >
      <Combobox endpoint="Combobox.Ordenes_Servicio" label="Orden De Servicio" id="ordenservicio" v-model="ordenservicio.orden_servicio"  placeholder="Buscar o nueva"/>
      <InputBasic id="folio" label="folio" type="text" v-model="presupuesto.folio"/>
      <InputBasic id="ordenseguimiento" label="Orden De Seguimiento" type="text" v-model="ordenservicio.orden_seguimiento"/>
      <InputBasic id="ubicacion" label="Ubicacion" type="text" v-model="ordenservicio.ubicacion"/>
    </div>
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-2" >
      <Datapicker label="Fecha Estimada" v-model="datosentrada.estimacion" :clearable="false" :time="true" :range="false" class="md:col-span-1 sm:col-span-2  "/>
      <InputBasic id="kilometraje" label="Kilometraje" type="number" v-model="datosentrada.kilometraje" placeholder="ej. 392.31"/>
      <Select id="gasolina" :canempty="true" v-model="datosentrada.gasolina" label="Gasolina" :options="options"></Select>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-2" >
      <Combobox endpoint="Combobox.Ordenes_Servicio" label="Administrador de Trasportes" id="administradortrasporte" v-model="ordenservicio.orden_servicio"  placeholder="Buscar o Crear"/>
      <Combobox endpoint="Combobox.Ordenes_Servicio" label="Jefe De Procesos" id="jefeproceso" v-model="ordenservicio.orden_servicio"  placeholder="Buscar o Crear"/>
      <Combobox endpoint="Combobox.Ordenes_Servicio" label="Trabajador" id="trabajador" v-model="ordenservicio.orden_servicio"  placeholder="Buscar o Crear"/>
      <Combobox endpoint="Combobox.Ordenes_Servicio" label="Tecnico" id="tecnico" v-model="ordenservicio.orden_servicio"  placeholder="Buscar o Crear"/>
    </div>
  </BaseModal>
</template>
2