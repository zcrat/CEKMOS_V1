<script setup lang="ts">
import { Dialog, DialogPanel, DialogOverlay, DialogTitle } from '@headlessui/vue'
import { computed } from 'vue'
import type { ConfirmButton } from '@/types/modals'

const props = withDefaults(defineProps<{
  show: boolean
  confirmButton?: ConfirmButton
  modalTitle?: string
  position?: 'start' | 'center' | 'end'
}>(), {
  position: 'start'
})
function closeModal() {
  emit('close')
}
const emit = defineEmits<{
  (e: 'close'): void
}>()

const titlePositionClass = computed(() => {
  switch (props.position) {
    case 'center':
      return 'text-center'
    case 'end':
      return 'text-right'
    default:
      return 'text-left'
  }
})


</script>
<template>
  <Dialog
    :open="show"
    class="relative z-50"
    @close="closeModal"
  >
    <DialogOverlay class="fixed inset-0 bg-black/40" />

    <div class="fixed inset-0 flex items-center justify-center">
      <DialogPanel
        class="max-w-screen w-full m-4 sm:w-auto p-6 bg-white rounded-xl shadow-xl max-h-[90vh] overflow-auto"
      >
        <DialogTitle
          v-if="modalTitle"
          :class="['text-lg font-semibold mb-2', titlePositionClass]"
        >
          {{ modalTitle }}
        </DialogTitle>

        <slot />

        <div class="mt-4 flex justify-end gap-4">
          <button
            v-if="confirmButton"
            :class="[
              'px-4 py-2 rounded-md capitalize',
              confirmButton.className ?? 'bg-gray-200',
              confirmButton.disabled ? 'opacity-50' : ''
            ]"
            :disabled="!!confirmButton.disabled"
            @click="confirmButton.onClick"
          >
            {{ confirmButton.text }}
          </button>

          <button
            class="px-4 py-2 bg-gray-200 rounded-md"
            @click="closeModal"
          >
            Cerrar
          </button>
        </div>
      </DialogPanel>
    </div>
  </Dialog>
</template>
