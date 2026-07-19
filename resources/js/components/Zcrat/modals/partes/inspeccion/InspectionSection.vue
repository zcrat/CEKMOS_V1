<script setup lang="ts">
withDefaults(defineProps<{
  title: string
  showNotes?: boolean
  disabled?: boolean
}>(), {
  showNotes: false,
  disabled: false,
})

const notes = defineModel<string>('notes', { default: '' })
</script>

<template>
  <section class="overflow-hidden rounded-lg border border-slate-300">
    <h4 class="bg-[#625a87] px-2 py-1.5 text-sm font-bold uppercase text-white">
      {{ title }}
    </h4>

    <slot />

    <label
      v-if="showNotes"
      class="flex flex-col gap-1 px-2 py-2 text-xs font-bold uppercase text-gray-700"
    >
      <span>Notas</span>
      <textarea
        v-model="notes"
        rows="3"
        maxlength="500"
        :disabled="disabled"
        class="w-full resize-y rounded border border-gray-400 px-2 py-1.5 text-sm font-normal normal-case text-gray-900"
      />
    </label>
  </section>
</template>
