<!-- ModalExample.vue -->
<script setup lang="ts">
import BaseModal from '@/Components/Zcrat/modals/BasicModal.vue'
import Inputbasic from '@/Components/Zcrat/Inputs/form/InputBasic.vue'
import MyBasicToast from '@/utils/ToastNotificationBasic'
import {buttonconfirmed} from '@/utils/interfaces/modals'
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
  <BaseModal modaltitle="Administrar Permisos" :modelValue="props.show" @update:modelValue="updateVisibility" :buttonconfirm="buttonconfirm">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
      <div>
        <h3 class="text-center">Permisos Asignados</h3>
        <div class="gap-2 flex flex-row">
          <button class="bg-red-400 px-1 rounded"><font-awesome-icon icon="fa-solid fa-trash-can"/></button>
          <label for="">Empleado</label>
        </div>

      </div>
      <div>
        <h3 class="text-center">Permisos Disponibles</h3>
        <div class="gap-2 flex flex-row">
          <button class="bg-green-500 px-1 rounded"><font-awesome-icon icon="fa-solid fa-plus"/></button>
          <label for="">Empleado</label>
        </div>
      </div>
    </div>
  </BaseModal>
</template>
