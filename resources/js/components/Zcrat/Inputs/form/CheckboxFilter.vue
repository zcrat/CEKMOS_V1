<script setup lang="ts">
const emit = defineEmits(['change'])

const props = defineProps<{
  classname?: string
  classlabel?: string
  classdiv?: string
  label: string
  value: string | number
  checked: boolean
  id: string
  name: string
}>()

const handleChange = (event: Event) => {
  emit('change', props.value)
}
</script><i class="fa-regular fa-square-check"></i>
<template>
  <div :class="['flex flex-row justify-start items-center gap-x-2 p-2 cursor-pointer', classdiv]" @click="handleChange">
    <font-awesome-icon :icon="checked? 'fa-solid fa-square-check':'fa-regular fa-square-check'" :class="'sm:text-[1.3rem]'"/>
    <h4 :class="classlabel ?? ''">
      {{ label }}
    </h4>
  </div>
</template>