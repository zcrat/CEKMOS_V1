<script setup lang="ts">
import type { ConfirmButton } from '@/types/modals'
import MyBasicToast from '@/utils/ToastNotificationBasic'
import { computed } from 'vue'
import {
  DialogClose,
  DialogContent,
  DialogDescription,
  DialogOverlay,
  DialogPortal,
  DialogRoot,
  DialogTitle,
} from 'reka-ui'
import Loading from '../Elements/Loading.vue'

const props = withDefaults(defineProps<{
  show: boolean
  loading?: boolean
  saving?: boolean
  modalDescription?: string
  confirmButton?: ConfirmButton
  modalTitle?: string
  savingMessage?: string
  loadingMessage?: string
  position?: 'start' | 'center' | 'end'
  zIndexClass?: 'z-[50]' | 'z-[999]' | ''
  classslot?: string
  modalClass?: string
}>(), {
  position: 'start',
  classslot: "overflow-auto sm:px-4",
  modalClass: 'w-fit max-w-screen',
  loading: false,
  zIndexClass: '',
})

const emit = defineEmits<{ (e: 'close'): void }>()

function closeModal() {
  if (props.saving) {
    if (props.savingMessage) {
      MyBasicToast.warning(props.savingMessage)
    }
  } else {
    emit('close')
  }
}

function handleOpenChange(open: boolean) {
  if (!open) {
    closeModal()
  }
}

const titlePositionClass = computed(() => ({
  start: 'text-left',
  center: 'text-center',
  end: 'text-right',
}[props.position]))
</script>

<template>
  <DialogRoot
    v-slot="{ close }"
    :open="show"
    @update:open="handleOpenChange"
  >
    <DialogPortal>
      <DialogOverlay :class="['fixed inset-0 bg-black/40', zIndexClass]" />
      <DialogContent
        :class="['fixed inset-0 flex items-center justify-center', zIndexClass]"
      >
        <div :class="['relative m-2 max-h-[90vh] overflow-auto rounded-xl bg-white px-4 shadow-xl max-w-[100vw]', modalClass]">
          <DialogDescription class="sr-only">
            {{ modalDescription ?? modalTitle ?? 'Ventana de diálogo' }}
          </DialogDescription>
          <div
            v-if="modalTitle"
            class="sticky top-0 z-10 flex items-center justify-between bg-white py-3"
          >
            <DialogTitle
              :class="['px-2 text-xl font-semibold', titlePositionClass]"
            >
              {{ modalTitle }}
            </DialogTitle>
            <DialogClose
              aria-label="Cerrar"
              @click="close"
            >
              <font-awesome-icon icon="fa-solid fa-x" class="text-[1rem]" />
            </DialogClose>
          </div>
          <Loading
            v-show="loading"
            :text="loadingMessage ?? 'Cargando'"
          />
          <div v-show="!loading" :class=classslot>
            <slot />
          </div>
          <div class="sticky bottom-0 z-10 flex items-center justify-end gap-4 bg-white py-2">
            <button
              class="h-10 rounded-md bg-[--color4] px-4 py-2"
              @click="close"
            >
              Cerrar
            </button>
            <slot name="footer" />
            <button
              v-if="confirmButton"
              :class="[
                'h-10 rounded-md px-4 py-2 capitalize',
                confirmButton.className ?? 'bg-[--micolor]',
                confirmButton.disabled ? 'opacity-50' : '',
              ]"
              :disabled="!!confirmButton.disabled"
              @click="confirmButton.onClick"
            >
              {{ confirmButton.text }}
            </button>
          </div>
        </div>
      </DialogContent>
    </DialogPortal>
  </DialogRoot>
</template>
