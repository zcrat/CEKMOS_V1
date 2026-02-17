<script setup lang="ts">
import { computed } from 'vue'

const props = withDefaults(defineProps<{
  isnew: boolean
  text: string
  classname?: string
}>(), {})

const emit = defineEmits<{
  (e: 'toggle', event: MouseEvent): void
}>()

const clases = computed(() =>
  props.isnew ? 'bg-green-700' : 'bg-red-400'
)

const icon = computed(() =>
  props.isnew ? 'fa-solid fa-plus' : 'fa-solid fa-trash-can'
)

function onToggle(event: MouseEvent) {
  emit('toggle', event)
}
</script>
<template>
  <div :class="['gap-2 flex flex-row', classname]">
    <button
      :class="[clases, 'px-1 rounded']"
      @click="onToggle"
    >
      <font-awesome-icon :icon="icon" />
    </button>

    <label class="break-words uppercase">
      {{ text }}
    </label>
  </div>
</template>