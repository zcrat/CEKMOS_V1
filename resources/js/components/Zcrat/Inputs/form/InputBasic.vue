<script setup lang="ts">
import {watch,ref } from 'vue'

const modelValue = defineModel<string | number |null>()
const inputEl = ref<HTMLInputElement | null>(null)

// 👇 expone el método para que desde fuera se pueda llamar a blur()
defineExpose({
  blur: () => inputEl.value?.blur(),
  focus: () => inputEl.value?.focus(),
})
const props = defineProps<{
  id: string
  classname?: string
  classdiv?: string
  placeholder?: string
  disabled?: boolean
  readonly?: boolean
  label?: string
  errors?: string[]
  icon?: string
  type?: 'text' | 'number' | 'password' | 'email'
  OnFocus?: ()=>void
  OnBlur?: ()=>void
}>()



</script>

<template>
  <div :class="['flex flex-col justify-start relative', classdiv]">
    <label v-if="props.label">{{ props.label }}</label>
    <div class="relative flex">
      <input
        ref="inputEl"
        :type="props.type || 'text'"
        :placeholder="props.placeholder"
        :class="['rounded-md inputfocus w-full', props.classname, icon ? 'ps-[2rem]' : '']"
        v-model="modelValue"
        :id="id"
        :readonly="props.readonly"
        :disabled="props.disabled"
        :name="id"
        @focus="OnFocus"
        @blur="OnBlur"
      />
      <font-awesome-icon
        v-if="props.icon"
        :icon="props.icon"
        class="absolute start-0 px-2 top-[50%] transform -translate-y-[50%] text-[1.25em]"
      />
    </div>
    <div v-if="props.errors">
      <h4  v-for="(value,index) in props.errors"  :key="index" class="text-red-500 font-light capitalize italic ">{{ value }}</h4>
    </div>
    
  </div>
</template>
