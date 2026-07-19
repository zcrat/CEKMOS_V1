<script setup lang="ts">
const props = withDefaults(defineProps<{
  modelValue: number | null
  label: string
  disabled?: boolean
}>(), {
  disabled: false,
})

const emit = defineEmits<{
  (event: 'update:modelValue', value: number): void
}>()

const statuses = [
  {
    id: 23,
    name: 'Requiere atención inmediata',
    shapeClass: 'border-2 border-gray-900 bg-[#ff1111]',
    checkClass: '',
  },
  {
    id: 24,
    name: 'Puede requerir atención futura',
    shapeClass: 'bg-[#ffc43d] [clip-path:polygon(50%_0,100%_100%,0_100%)]',
    checkClass: 'translate-y-1.5 text-lg',
  },
  {
    id: 25,
    name: 'Inspeccionado y en buen estado',
    shapeClass: 'rounded-full border-2 border-gray-900 bg-[#08b83f]',
    checkClass: '',
  },
] as const
</script>

<template>
  <div class="flex min-w-0 items-center gap-2">
    <div class="inline-flex shrink-0 gap-1.5" role="radiogroup" :aria-label="label">
      <button
        v-for="status in statuses"
        :key="status.id"
        type="button"
        class="flex size-10 items-center justify-center rounded bg-transparent p-1 hover:bg-blue-100 focus-visible:ring-2 focus-visible:ring-[#2f6887] disabled:cursor-not-allowed disabled:opacity-50"
        :class="{ 'bg-blue-100 ring-2 ring-[#2f6887]': modelValue === status.id }"
        :aria-label="`${label}: ${status.name}`"
        :aria-checked="modelValue === status.id"
        :title="status.name"
        role="radio"
        :disabled="disabled"
        @click="emit('update:modelValue', status.id)"
      >
        <span
          class="flex size-8 items-center justify-center text-gray-900"
          :class="status.shapeClass"
        >
          <span
            v-if="modelValue === status.id"
            class="text-2xl font-black leading-none"
            :class="status.checkClass"
          >✓</span>
        </span>
      </button>
    </div>
    <span class="min-w-0 text-sm font-semibold leading-tight text-gray-800">{{ label }}</span>
  </div>
</template>
