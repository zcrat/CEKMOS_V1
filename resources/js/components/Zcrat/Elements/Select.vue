<script setup lang="ts">
import {option} from '@/utils/interfaces/generales'
const props = defineProps<{
  label?: string
  id: string
  canempty?:boolean
  options:option[]
}>()
const selected = defineModel<string|number>();
</script>
<template>
    
    <div class="flex flex-col">
      <label for="" v-if="props.label">{{props.label }}</label>
      <select :name="props.id" :id="props.id" v-model="selected" class="focus-input rounded-md">
          <option value="" v-show="!selected" v-if="canempty">Seleccionar Opcion</option>
          <option v-for="opt in props.options" :value="opt.value">{{ opt.label }}</option>
      </select>
    </div>
</template>