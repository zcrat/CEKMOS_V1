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
  @update:open="() => closeModal()"
  v-slot="{ close }"
>
    <DialogPortal >
      <DialogOverlay class="fixed inset-0 bg-black/40" />
      <DialogContent class="fixed inset-0 flex items-center justify-center">
        <div class=" relative max-w-screen w-full m-4 sm:w-auto px-6 bg-white rounded-xl shadow-xl max-h-[90vh] overflow-auto">
          <div v-if="modaltitle" class="flex justify-between items-center pt-4 pb-2 sticky top-0 z-10 bg-white">
          <DialogTitle
          
            :class="['text-xl font-semibold', classtitleposition]"
          >
            {{ modaltitle }}
          </DialogTitle>
          <DialogClose
              aria-label="Close"
              @click="close"
            >
            <font-awesome-icon icon="fa-solid fa-x" class="text-[1rem]"/>
          </DialogClose>
          </div>
          <div class="sm:px-4 overflow-auto">
            <slot />
          </div>
          <div class="pt-2 pb-4 flex justify-end items-center gap-4 sticky bottom-0 z-10 bg-white ">
            <button
              class="px-4 py-2 bg-[--color4] h-10 rounded-md"
              @click="close"
            >
              Cerrar
            </button>
            <button
              v-if="buttonconfirm"
              :class="[
                'px-4 py-2 rounded-md capitalize h-10',
                buttonconfirm.classname ?? 'bg-[--micolor]',
                buttonconfirm.disabled ? 'opacity-50' : ''
              ]"
              :disabled="!!buttonconfirm.disabled"
              @click="buttonconfirm.onClick"
            >
              {{ buttonconfirm.text }}
            </button>

            
          </div>
        </div>
      </DialogContent>
    </DialogPortal>
  </DialogRoot>
</template>