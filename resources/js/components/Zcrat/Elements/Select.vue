<script setup lang="ts">
import {option} from '@/types/generales'
const props = defineProps<{
  label?: string
  optionlabelempty?: string
  id?: string
  canempty?:boolean
  options:option[]
}>()
const selected = defineModel<string|number>();
</script>
<template>
    
    <div class="flex flex-col">
      <label for="" v-if="props.label">{{props.label }}</label>
      <select :name="props.id" :id="props.id" v-model="selected" class="inputfocus rounded-md">
          <option value="" v-show="canempty || !selected" v-if="canempty || !selected">{{optionlabelempty ??'Seleccionar'}}</option>
          <option v-for="opt in props.options" :key="'option-'+opt.value" :value="opt.value">{{ opt.label }}</option>
      </select>
    </div>
</template>