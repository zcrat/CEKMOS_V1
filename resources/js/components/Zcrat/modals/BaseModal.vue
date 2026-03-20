<script setup lang="ts">
import { computed } from 'vue'
import type { buttonconfirmed } from '@/types/modals'
import {
  DialogClose,
  DialogContent,
  DialogOverlay,
  DialogPortal,
  DialogRoot,
  DialogTitle,
} from 'reka-ui'

const props = withDefaults(defineProps<{
  show: boolean
  buttonconfirm?: buttonconfirmed
  modaltitle?: string
  position?: 'start' | 'center' | 'end'
}>(), {
  position: 'start',
})

const emit = defineEmits<{ (e: 'close'): void }>()

function closeModal() {
  emit('close')
}

const classtitleposition = computed(() => ({
  start: 'text-left',
  center: 'text-center',
  end: 'text-right',
}[props.position]))
</script>

<template>
  <DialogRoot
    :open="show"
    @close="closeModal"
  >
    <DialogPortal >
      <DialogOverlay class="fixed inset-0 bg-black/40" />
      <DialogContent
        class="fixed inset-0 max-w-screen w-full sm:m-4 sm:w-auto p-2 bg-white rounded-xl shadow-xl max-h-[90vh]"
      >
        <div v-if="modaltitle" class="flex justify-between items-center px-4 ">
        <DialogTitle
         
          :class="['text-xl font-semibold', classtitleposition]"
        >
          {{ modaltitle }}
        </DialogTitle>
        <DialogClose
            aria-label="Close"
            @click="closeModal"
          >
          <font-awesome-icon icon="fa-solid fa-x" class="text-[1rem]"/>
        </DialogClose>
        </div>
        <div class="overflow-auto h-[90%] sm:px-4">
          <slot />
        </div>
        <div class="pt-2 flex justify-end gap-4">
          <button
            class="px-4 py-2 bg-[--color4] rounded-md"
            @click="closeModal"
          >
            Cerrar
          </button>
          <button
            v-if="buttonconfirm"
            :class="[
              'px-4 py-2 rounded-md capitalize',
              buttonconfirm.classname ?? 'bg-[--micolor]',
              buttonconfirm.disabled ? 'opacity-50' : ''
            ]"
            :disabled="!!buttonconfirm.disabled"
            @click="buttonconfirm.onClick"
          >
            {{ buttonconfirm.text }}
          </button>

          
        </div>
      </DialogContent>
    </DialogPortal>
  </DialogRoot>
</template>