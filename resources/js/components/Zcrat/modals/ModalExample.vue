<!-- ModalExample.vue -->
<script setup lang="ts">
import BaseModal from '@/components/Zcrat/modals/BasicModal.vue'
import TextSimple from '@/components/Zcrat/Inputs/form/TextSimple.vue'
import NumberSimple from '@/components/Zcrat/Inputs/form/NumberSimple.vue'
import MyBasicToast from '@/utils/ToastNotificationBasic'
import {buttonconfirmed} from '@/utils/interfaces/modals'
const props = defineProps<{ show: boolean }>()
const emit = defineEmits(['update:show'])

const updateVisibility = (val: boolean) => {
  emit('update:show', val)
}


const functionexample =()=>{
    MyBasicToast.success('Pruebas')
  MyBasicToast.error('Pruebas')
}
const buttonconfirm:buttonconfirmed ={
  text:'aceptar',
  classname:'bg-[--btnprimary] text-white',
  onClick:functionexample
}
</script>

<template>
  <BaseModal modaltitle="Registrar Nuevo Empleado" :modelValue="props.show" @update:modelValue="updateVisibility" :buttonconfirm="buttonconfirm">
    <form action="">
      <div class="flex flex-col sm:flex-row gap-1 sm:w-[40rem]">
        <TextSimple label="Nombre(s)" key="nombre" icon="fa-solid fa-user" classdiv="flex-grow"/> 
        <TextSimple label="Apellido Paterno" key="paterno" />
        <TextSimple label="Apellido Materno" key="materno"/>
      </div>
      <div class="flex flex-col sm:flex-row gap-1 sm:w-[40rem]">
        <TextSimple label="CURP" key="curp" classdiv="flex-grow"/>
        <TextSimple label="RFC" key="rfc" classdiv="flex-grow"/>
      </div>
      <div class="flex flex-col sm:flex-row gap-1 sm:w-[40rem]">
        <NumberSimple label="Domicilio Fiscal" key="domicilio_fiscal" classdiv="flex-grow"/>
        <NumberSimple label="Regimen Fiscal" key="regimen_fiscal_id" classdiv="flex-grow"/>
        <NumberSimple label="Uso CFDI" key="uso_cfdi_id" classdiv="flex-grow"/>
      </div>
    </form>
  </BaseModal>
</template>
