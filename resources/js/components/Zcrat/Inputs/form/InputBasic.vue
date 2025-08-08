<script setup lang="ts">
import { defineProps, defineModel, watch } from 'vue'

const modelValue = defineModel<string | number |null>()

const props = defineProps<{
  id: string
  classname?: string
  classdiv?: string
  placeholder?: string
  label?: string
  icon?: string
  type?: 'text' | 'number'
}>()

// 👇 Si el input es tipo "number", forzamos el valor como número
watch(modelValue, (val) => {
  if (val === '' || val === null) {
      modelValue.value = null
  }else if (props.type === 'number') {
    if (typeof val === 'string') {
      const numeric = Number(val)
      if (!isNaN(numeric)) {
        modelValue.value = numeric
      } else {
        modelValue.value = null
      }
    }
  }
})

</script>

<template>
  <div :class="['flex flex-col justify-start relative', classdiv]">
    <label v-if="props.label">{{ props.label }}</label>
    <div class="relative flex">
      <input
        :type="props.type || 'text'"
        :placeholder="props.placeholder"
        :class="['rounded-md focus-input w-full', props.classname, icon ? 'ps-[2rem]' : '']"
        v-model="modelValue"
        :id="id"
        :name="id"
      />
      <font-awesome-icon
        v-if="props.icon"
        :icon="props.icon"
        class="absolute start-0 px-2 top-[50%] transform -translate-y-[50%] text-[1.25em]"
      />
    </div>
  </div>
</template>
