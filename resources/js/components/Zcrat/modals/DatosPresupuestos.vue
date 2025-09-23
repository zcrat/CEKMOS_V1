<!-- ModalExample.vue -->
<script setup lang="ts">

import InputBasic from '../Inputs/form/InputBasic.vue'
import BaseModal from '@/components/Zcrat/modals/BasicModal.vue'
import ButtonTogglePYR from '@/components/Zcrat/Inputs/ButtonTogglePYR.vue'
import MyBasicToast from '@/utils/ToastNotificationBasic'
import axios from 'axios'
import { ref, watch ,reactive} from 'vue' 
import Loanding from '@/components/Zcrat/Elements/Loanding.vue';
import type { DatosPresupuestos,DatosOrdenServicio } from '@/utils/interfaces/generales'


const props = defineProps<{show: boolean}>()
const emit = defineEmits(['update:show'])

const updateVisibility = (val: boolean) => {
  emit('update:show', val)
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
</script>

<template>
  <BaseModal modaltitle="Presupuestos" :position="'center'" :modelValue="props.show" @update:modelValue="updateVisibility" >
    <div class="grid grid-cols-2 md:grid-cols-4 gap-2" >
      <InputBasic id="ordenservicio" label="Orden De Servicio" type="text" v-model="ordenservicio.orden_servicio"/>
      <InputBasic id="Folio" label="Folio" type="text" v-model="presupuesto.folio"/>
      <InputBasic id="ordenseguimiento" label="Orden De Seguimiento" type="text" v-model="ordenservicio.orden_seguimiento"/>
      <InputBasic id="ubicacion" label="Ubicacion" type="text" v-model="ordenservicio.ubicacion"/>
    </div>
  </BaseModal>
</template>
