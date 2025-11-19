<!-- ModalExample.vue -->
<script setup lang="ts">
import BaseModal from '@/components/Zcrat/modals/BasicModal.vue'
import Inputbasic from '@/components/Zcrat/Inputs/form/InputBasic.vue'
import MyBasicToast from '@/utils/ToastNotificationBasic'
import {buttonconfirmed} from '@/types/modals'
import { reactive, computed, watchEffect } from 'vue' 

interface NewEmployee {
  nombre: string | null,
  paterno: string | null,
  materno: string | null,
  curp: string | null,
  rfc: string | null,
  domicilio_fiscal: number | null,
  regimen_fiscal_id: number | null,
}
const props = defineProps<{ show: boolean }>()
const emit = defineEmits(['update:show'])

const updateVisibility = (val: boolean) => {
  emit('update:show', val)
}

const newEmployee = reactive<NewEmployee>({ // Use reactive to make newEmployee reactive
  nombre: null,
  paterno: null,
  materno: null,
  curp: null,
  rfc: null,
  domicilio_fiscal: null,
  regimen_fiscal_id: null
})

const isFormValid = computed(() => { 
  return (
    newEmployee.nombre != null &&
    newEmployee.paterno != null &&
    newEmployee.materno != null &&
    newEmployee.curp != null &&
    newEmployee.rfc != null &&
    newEmployee.domicilio_fiscal != null &&
    newEmployee.regimen_fiscal_id != null
  )
})


const functionexample = async ()=>{
  if (!isFormValid.value) {
    MyBasicToast.error('Todos los campos son obligatorios')
    return
  }
  

  MyBasicToast.success('Empleado registrado exitosamente')
  updateVisibility(false)
}
const buttonconfirm = computed((): buttonconfirmed => ({
  text: 'aceptar',
  classname: 'bg-[--btnprimary] text-white',
  onClick: functionexample,
  disabled: !isFormValid.value, // o !isFormValid.value si cambias nombre
}))

</script>

<template>
  <BaseModal modaltitle="Registrar Nuevo Empleado" :modelValue="props.show" @update:modelValue="updateVisibility" :buttonconfirm="buttonconfirm">
    <form action="">
      <div class="flex flex-col sm:flex-row gap-1 sm:w-[40rem]">
        <Inputbasic label="Nombre(s)" id="nombre" type="text" v-model="newEmployee.nombre" icon="fa-solid fa-user" classdiv="flex-grow"/> 
        <Inputbasic label="Apellido Paterno" type="text" v-model="newEmployee.paterno" id="paterno" />
        <Inputbasic label="Apellido Materno" type="text" v-model="newEmployee.materno" id="materno"/>
      </div>
      <div class="flex flex-col sm:flex-row gap-1 sm:w-[40rem]">
        <Inputbasic label="CURP" id="curp" type="text" v-model="newEmployee.curp" classdiv="flex-grow"/>
        <Inputbasic label="RFC" id="rfc" type="text" v-model="newEmployee.rfc" classdiv="flex-grow"/>
      </div>
      <div class="flex flex-col sm:flex-row gap-1 sm:w-[40rem]">
        <Inputbasic label="Domicilio Fiscal" id="domicilio_fiscal" type="number" v-model="newEmployee.domicilio_fiscal" classdiv="flex-grow"/>
        <Inputbasic label="Regimen Fiscal" id="regimen_fiscal_id" type="number" v-model="newEmployee.regimen_fiscal_id" classdiv="flex-grow"/>
      </div>
    </form>
  </BaseModal>
</template>
