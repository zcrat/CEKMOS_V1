<script setup lang="ts">
import { defineProps } from 'vue'

const modelValue = defineModel<string | number |null>()
const props=defineProps<{
  id:string
  classname?: string
  classdiv?: string
  placeholder?: string
  label?: string
}>()

</script>
<template>
  <div :class="['flex flex-col justify-start relative', classdiv]">
    <label v-if="props.label">{{props.label}}</label>
    <textarea :placeholder="props.placeholder" :class="['rounded-md focus-input w-full',props.classname]" v-model="modelValue" :id="id" :name="id"></textarea>
  </div>
</template>