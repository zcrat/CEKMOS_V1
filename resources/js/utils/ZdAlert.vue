<script setup lang="ts">
import { ref, onMounted, onUnmounted } from 'vue';
import BaseModal from '@/components/Zcrat/modals/BaseModal.vue';
import { registerAlert, unregisterAlert, type AlertOptions } from '@/utils/ZdAlert';

const open = ref(false);
const options = ref<AlertOptions>({});
const queue: ((v: boolean) => void)[] = [];
let resolver: (v: boolean) => void = () => {};

function fire(opts: AlertOptions) {
  options.value = opts;
  open.value = true;

  return new Promise<boolean>((resolve) => {
    resolver = resolve;
  });
}

onMounted(() => registerAlert(fire));
onUnmounted(() => unregisterAlert());

function handleClose(result: boolean) {
  open.value = false;
  resolver(result);
}
</script>

<template>


  <BaseModal
    :show="open"
    :modaltitle="'Atencion'"
    :z="'z-[999]'"
    @close="handleClose(false)"
    :buttonconfirm="{
      text: options.confirmButtonText || 'Aceptar',
      onClick: () => handleClose(true)
    }"
  >
  <div class="p-6">
    {{ options.text || '¿Deseas continuar?' }}
  </div>
  </BaseModal>
    <slot />
</template>