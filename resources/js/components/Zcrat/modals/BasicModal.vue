<script setup lang="ts">
    import { Dialog, DialogPanel, DialogOverlay, DialogTitle } from '@headlessui/vue'
    import {buttonconfirmed} from '@/utils/interfaces/modals'
    import { computed } from 'vue'


    const props = withDefaults(defineProps<{
      modelValue: boolean
      buttonconfirm?:buttonconfirmed
      modaltitle?:string
      position?: 'start' | 'center' | 'end'
    }>(), {
      position: 'start'
    })
    const emit = defineEmits(['update:modelValue'])

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
    const close = () => {
        emit('update:modelValue', false)
    }
</script>
<template>
    <Dialog :open="modelValue" class="relative z-50">
    <DialogOverlay class="fixed inset-0 bg-black/40" />

    <div class="fixed inset-0 flex items-center justify-center">
      <DialogPanel class="max-w-screen w-full m-4 sm:w-auto p-6 bg-white rounded-xl shadow-xl">
        <DialogTitle v-if="modaltitle" :class="['text-lg font-semibold mb-2', classtitleposition]">{{modaltitle}}</DialogTitle>
        <slot />
        <div class="mt-4 flex justify-end gap-4">
          <button v-if="buttonconfirm" :class="['px-4 py-2 rounded-md capitalize', buttonconfirm.classname ??'bg-gray-200',(buttonconfirm.disabled==true? 'opacity-50':'')]" :disabled="(buttonconfirm.disabled? buttonconfirm.disabled:false)" @click="buttonconfirm.onClick">{{buttonconfirm.text}}</button>
          <button class="px-4 py-2 bg-gray-200 rounded-md" @click="close">Cerrar</button>
        </div>
      </DialogPanel>
    </div>
  </Dialog>
</template>