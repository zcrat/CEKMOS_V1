<!-- ModalExample.vue -->
<script setup lang="ts">
import { ref } from 'vue';
import BaseModal from './BaseModal.vue';
import Loading from '../Elements/Loading.vue';

  const show = ref<boolean>(false);
  const id = ref<number | null>(null);
  const loading = ref<boolean>(false);
  const tipo = ref<1|2|null>(null);

  const updateVisibility = () => {
    show.value = false;
    id.value = null;
    tipo.value = null;
    loading.value = false;
  }
  const Open = (Id: number | null,Tipo:1|2) => {
    id.value = Id;
    show.value = Id !== null;
    loading.value = Id !== null;
    tipo.value=Tipo;
  }

  const finishLoading = () => {
    loading.value = false;
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
  <div class="relative h-[80vh] md:w-[80vw] lg:w-[50vw] w-[90vw] max-w-screen-xl">
    <div
      v-if="loading"
      class="absolute inset-0 z-10 flex items-center justify-center bg-white"
    >
      <Loading text="Generando PDF" />
    </div>

    <iframe
      v-if="id !== null && tipo != null"
      :src="tipo == 1 ? route('pdf.cortana.recepcionvehicular', { id }) : route('pdf.cortana.inspeccion.vehicular', { id })"
      class="block h-full w-full border-0"
      @load="finishLoading"
    />
  </div>
  </BaseModal>
</template>
