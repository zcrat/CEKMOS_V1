<script setup lang="ts">
import { computed } from 'vue'

const props = defineProps<{
  title: string
  tone: 'orange' | 'purple'
}>()

const borderColor = computed(() => props.tone === 'orange'
  ? 'border-[#c65a42]'
  : 'border-[#625a87]')

const titleColor = computed(() => props.tone === 'orange'
  ? 'bg-[#c65a42]'
  : 'bg-[#625a87]')
</script>

<template>
  <article
    class="overflow-hidden rounded-lg border-4 bg-white"
    :class="borderColor"
  >
    <h3
      class="px-3 py-2 font-bold uppercase text-white"
      :class="titleColor"
    >
      {{ title }}
    </h3>

    <div class="grid gap-3 p-3 lg:grid-cols-2">
      <slot />
    </div>
  </article>
</template>
