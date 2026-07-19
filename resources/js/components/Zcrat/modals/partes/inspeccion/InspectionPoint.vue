<script setup lang="ts">
import InspectionStatusButtons from '@/components/Zcrat/Inputs/InspectionStatusButtons.vue'
import type { InspectionStatus } from '@/types/InspeccionVehicular'

withDefaults(defineProps<{
  label: string
  disabled?: boolean
  showPressure?: boolean
  showChecks?: boolean
}>(), {
  disabled: false,
  showPressure: false,
  showChecks: false,
})

const status = defineModel<InspectionStatus>('status', { required: true })
const pressure = defineModel<number>('pressure', { default: 0 })
const ok = defineModel<boolean>('ok', { default: false })
const lleno = defineModel<boolean>('lleno', { default: false })
</script>

<template>
  <div class="grid grid-cols-[1fr_auto] items-center gap-2 border-b border-gray-200 px-2 py-1.5 max-sm:grid-cols-1">
    <InspectionStatusButtons
      v-model="status"
      :label="label"
      :disabled="disabled"
    />

    <label v-if="showPressure" class="flex items-center gap-1 text-xs font-semibold text-gray-700">
      <span>Presión</span>
      <input
        v-model.number="pressure"
        type="number"
        min="0"
        max="999.99"
        step="0.01"
        :disabled="disabled"
        class="w-20 rounded border border-gray-400 px-2 py-1"
      >
    </label>

    <div v-if="showChecks" class="flex items-center gap-2">
      <label class="inline-flex items-center gap-1 text-xs font-semibold text-gray-700">
        <input
          v-model="ok"
          type="checkbox"
          :disabled="disabled"
          class="size-5"
        >
        OK
      </label>
      <label class="inline-flex items-center gap-1 text-xs font-semibold text-gray-700">
        <input
          v-model="lleno"
          type="checkbox"
          :disabled="disabled"
          class="size-5"
        >
        Lleno
      </label>
    </div>
  </div>
</template>
