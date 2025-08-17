<script setup lang="ts">
import { defineProps } from 'vue'

const props=defineProps<{
  id:string
  classname?: string
  classdiv?: string
  placeholder?: string
  label?: string
  value?: any
  icon?:string
}>()

</script>
<template>
  <div :class="['flex flex-col justify-start relative', classdiv]">
    <label v-if="props.label">{{props.label}}</label>
    <div class="relative flex">
      <input type="number" :placeholder="props.placeholder" :class="['rounded-md focus-input w-full',props.classname,icon?'ps-[2rem]':'']" :value="props.value" :id="id" :name="id">
      <font-awesome-icon  v-if="props.icon" :icon="props.icon" class="absolute start-0 px-2 top-[50%] transform -translate-y-[50%] text-[1.25em] "/>
    </div>
  </div>
</template>