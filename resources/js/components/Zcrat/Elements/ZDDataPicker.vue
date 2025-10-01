<script setup lang="ts">
import VueDatePicker from '@vuepic/vue-datepicker';
import '@vuepic/vue-datepicker/dist/main.css'
import { ref } from 'vue';

const date = defineModel<Date>();
const props = withDefaults(defineProps<{
  label?: string
  clearable?:boolean
  time?:boolean
  range?:boolean
  class?:string

}>(),{
  clearable:true,
  time:false,
  range:true,
})
</script>
<template>
    <div :class="['h-full flex flex-col',props.class??'w-[16rem]']">
      <label for="" v-if="props.label">{{props.label }}</label>
      <div class="border-gray-500 border rounded-md">
        <VueDatePicker v-model="date" :range="range?{ partialRange: false }:false" 
        :locale="'es'"
        auto-apply
        :ui="{ input:'py-8 focus-input' }"
        :style="{ '--dp-input-padding': '0.5rem'}"
        :enable-time-picker="time" :placeholder="'Seleccionar Fechas'" :clearable="clearable">
        </VueDatePicker>
      </div>
    </div>
</template>
         <!-- <VueDatePicker v-model="date" :range="{ partialRange: false } " 
         :locale="'es'"
         auto-apply
         :ui="{ input:'py-8' }"
          :style="{ '--dp-input-padding': 'auto' }"
         :enable-time-picker="false" :placeholder="'Seleccionar Rango De Fechas'" :clearable="true"><template #dp-input="{ value, onInput, onFocus, onBlur }">
    <input
      :value="value"
      @input="onInput"
      @focus="onFocus"
      @blur="onBlur"
      class="rounded-md border border-gray-300 focus:border-blue-500 focus:ring-blue-500 px-3 py-2 w-full"
      placeholder="Selecciona un día"
    />
  </template></VueDatePicker> -->