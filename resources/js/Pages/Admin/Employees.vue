<!-- Vista que usa ModalExample.vue -->
<script setup lang="ts">
import AppLayout from '@/Layouts/AppLayout.vue'
import Search from '@/components/Zcrat/Inputs/Search.vue'
import ButtonComponent from '@/components/Zcrat/Inputs/Button.vue'
import MyBasicToast from '@/utils/ToastNotificationBasic'
import ModalExample from '@/components/Zcrat/modals/ModalExample.vue'
import { ref } from 'vue'

const ModalExampleShoW = ref(false)

const NuevoEmpleado = () => {
  MyBasicToast.success('Pruebas')
  MyBasicToast.error('Pruebas')
}
</script>

<template>
  <AppLayout title="Empleados" description="Bienvenido al sistema CEKMOS">
    <template #header></template>

    <div class="h-full flex flex-col items-center justify-center sm:px-4">
      <div class="flex flex-row justify-start py-4 w-full">
        <ButtonComponent
          text="nuevo"
          icon="fa-solid fa-circle-plus"
          :onClick="() => (ModalExampleShoW = true)"
        />
        <Search Classdiv="sm:w-[20rem] w-full" />
      </div>
      <div class="flex h-full w-full p-8"></div>

      
      <ModalExample v-model:show="ModalExampleShoW" />
    </div>
  </AppLayout>
</template>
