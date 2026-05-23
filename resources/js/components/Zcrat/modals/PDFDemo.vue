<!-- ModalExample.vue -->
<script setup lang="ts">
import { ref } from 'vue';
import BaseModal from './BaseModal.vue';

  const show = ref<boolean>(false);

  const updateVisibility = () => {
    show.value = false;
  }
  const Open = (Id: number | null) => {show.value = true;}

  defineExpose({
    Open
  })
</script>

<!-- Vue -->

<template>
  <BaseModal modaltitle="Nueva Orden De Servicio" 
  :position="'center'"
  :show="show" 
  @close="updateVisibility" 
  >
  <iframe
    :src="route('Pdf.Cortana.RecepcionVehicular')"
    width="100%"
    height="800"
    style="border:none;"
  />
  </BaseModal>
</template>
