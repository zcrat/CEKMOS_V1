<!-- ModalExample.vue -->
<script setup lang="ts">
import { ref } from 'vue';
import BaseModal from './BaseModal.vue';

  const show = ref<boolean>(false);
  const id = ref<number | null>(null);

  const updateVisibility = () => {
    show.value = false;
    id.value = null;
  }
  const Open = (Id: number | null) => {
    id.value = Id;
    show.value = Id !== null;
  }

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
  <div class="h-[80vh] md:w-[80vw] lg:w-[50vw] w-[90vw] max-w-screen-xl">

    <iframe
      v-if="id !== null"
      :src="route('pdf.cortana.recepcionvehicular', { id })"
      class="block h-full w-full border-0"
    />
  </div>
  </BaseModal>
</template>
