<script setup lang="ts">
const emit = defineEmits(['change'])

const props = defineProps<{
  classname?: string
  classlabel?: string
  classdiv?: string
  label?: string
  value: string | number
  checked: boolean
  id: string
  name: string
}>()

const handleChange = (event: Event) => {
  emit('change', props.value)
}
</script>
<template>
  <div :class="['flex flex-row justify-start items-center gap-x-2 p-2', classdiv]" @onClick="handleChange">
    <input
      :id="id"
      :name="name"
      type="checkbox"
      :checked="checked"
      :disabled="true"
      :class="classname ?? 'rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500'"
    >
    <label v-if="label" :class="classlabel ?? ''" :for="id">
      {{ label }}
    </label>
  </div>
</template>