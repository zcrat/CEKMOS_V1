<script setup lang="ts">
import { Dialog, DialogPanel, DialogOverlay, DialogTitle } from '@headlessui/vue'
import { computed } from 'vue'
import type { buttonconfirmed } from '@/types/modals'

const props = withDefaults(defineProps<{
  modelValue: boolean
  buttonconfirm?: buttonconfirmed
  modaltitle?: string
  position?: 'start' | 'center' | 'end'
}>(), {
  position: 'start'
})

const emit = defineEmits<{
  (e: 'update:modelValue', value: boolean): void
  (e: 'close'): void
}>()

const classtitleposition = computed(() => {
  switch (props.position) {
    case 'center':
      return 'text-center'
    case 'end':
      return 'text-right'
    default:
      return 'text-left'
  }
})

function closeModal() {
  emit('update:modelValue', false)
  emit('close')
}
</script>
<template>
  <Dialog
    :open="modelValue"
    class="relative z-50"
    @close="closeModal"
  >
    <DialogOverlay class="fixed inset-0 bg-black/40" />

    <div class="fixed inset-0 flex items-center justify-center">
      <DialogPanel
        class="max-w-screen w-full m-4 sm:w-auto p-6 bg-white rounded-xl shadow-xl max-h-[90vh] overflow-auto"
      >
        <DialogTitle
          v-if="modaltitle"
          :class="['text-lg font-semibold mb-2', classtitleposition]"
        >
          {{ modaltitle }}
        </DialogTitle>

        <slot />

        <div class="mt-4 flex justify-end gap-4">
          <button
            v-if="buttonconfirm"
            :class="[
              'px-4 py-2 rounded-md capitalize',
              buttonconfirm.classname ?? 'bg-gray-200',
              buttonconfirm.disabled ? 'opacity-50' : ''
            ]"
            :disabled="!!buttonconfirm.disabled"
            @click="buttonconfirm.onClick"
          >
            {{ buttonconfirm.text }}
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