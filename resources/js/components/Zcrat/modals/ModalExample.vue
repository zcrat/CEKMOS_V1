<!-- ModalExample.vue -->
<script setup lang="ts">
import BaseModal from '@/components/Zcrat/modals/BasicModal.vue'
import TextSimple from '@/components/Zcrat/Inputs/form/TextSimple.vue'
import NumberSimple from '@/components/Zcrat/Inputs/form/NumberSimple.vue'
import MyBasicToast from '@/utils/ToastNotificationBasic'
import {buttonconfirmed} from '@/utils/interfaces/modals'
import { reactive, computed } from 'vue' 
interface NewEmployee {
  nombre: string | null,
  paterno: string | null,
  materno: string | null,
  curp: string | null,
  rfc: string | null,
  domicilio_fiscal: number | null,
  regimen_fiscal_id: number | null,
  uso_cfdi_id: number | null,
}
const props = defineProps<{ show: boolean }>()
const emit = defineEmits(['update:show'])

const updateVisibility = (val: boolean) => {
  emit('update:show', val)
}

const newEmployee: NewEmployee = reactive({ // Use reactive to make newEmployee reactive
  nombre: null,
  paterno: null,
  materno: null,
  curp: null,
  rfc: null,
  domicilio_fiscal: null,
  regimen_fiscal_id: null,
  uso_cfdi_id: null,
})

const isFormValid:boolean = computed(() => { // Create a computed property for validation
  return (
    newEmployee.nombre &&
    newEmployee.paterno &&
    newEmployee.materno &&
    newEmployee.curp &&
    newEmployee.rfc &&
    newEmployee.domicilio_fiscal &&
    newEmployee.regimen_fiscal_id &&
    newEmployee.uso_cfdi_id
  )
})


const functionexample = async ()=>{
  if (isFormValid.value === false) {
    MyBasicToast.error('Todos los campos son obligatorios')
    return
  }
  

  MyBasicToast.success('Empleado registrado exitosamente')
  updateVisibility(false)
}
const buttonconfirm:buttonconfirmed ={
  text:'aceptar',
  classname:'bg-[--btnprimary] text-white',
  onClick:functionexample,
  disabled: (!newEmployee.nombre || !newEmployee.paterno || !newEmployee.materno || !newEmployee.curp || !newEmployee.rfc || !newEmployee.domicilio_fiscal || !newEmployee.regimen_fiscal_id || !newEmployee.uso_cfdi_id), // Puedes cambiar esto según la lógica de tu aplicación
}
</script>

<template>
  <BaseModal modaltitle="Registrar Nuevo Empleado" :modelValue="props.show" @update:modelValue="updateVisibility" :buttonconfirm="buttonconfirm">
    <form action="">
      <div class="flex flex-col sm:flex-row gap-1 sm:w-[40rem]">
        <TextSimple label="Nombre(s)" id="nombre" v-model="newEmployee.nombre" icon="fa-solid fa-user" classdiv="flex-grow"/> 
        <TextSimple label="Apellido Paterno" v-model="newEmployee.paterno" id="paterno" />
        <TextSimple label="Apellido Materno" v-model="newEmployee.materno" id="materno"/>
      </div>
      <div class="flex flex-col sm:flex-row gap-1 sm:w-[40rem]">
        <TextSimple label="CURP" id="curp" v-model="newEmployee.curp" classdiv="flex-grow"/>
        <TextSimple label="RFC" id="rfc" v-model="newEmployee.rfc" classdiv="flex-grow"/>
      </div>
      <div class="flex flex-col sm:flex-row gap-1 sm:w-[40rem]">
        <NumberSimple label="Domicilio Fiscal" id="domicilio_fiscal" v-model="newEmployee.domicilio_fiscal" classdiv="flex-grow"/>
        <NumberSimple label="Regimen Fiscal" id="regimen_fiscal_id" v-model="newEmployee.regimen_fiscal_id" classdiv="flex-grow"/>
        <NumberSimple label="Uso CFDI" id="uso_cfdi_id" v-model="newEmployee.uso_cfdi_id" classdiv="flex-grow"/>
      </div>
    </form>
  </BaseModal>
</template>
