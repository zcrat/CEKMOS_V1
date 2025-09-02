<script setup lang="ts">
import VueDatePicker from '@vuepic/vue-datepicker';
import '@vuepic/vue-datepicker/dist/main.css'
import { ref } from 'vue';

const date = ref();
const props = defineProps<{
  label?: string
}>()
</script>
<template>
    <div class="w-[16rem] h-full flex flex-col border rounded border-gray-400">
        <label for="" v-if="props.label">{{props.label }}</label>
         <VueDatePicker v-model="date" :range="{ partialRange: false } " 
         :locale="'es'"
         auto-apply
         :ui="{ input:'py-8' }"
          :style="{ '--dp-input-padding': '0.5rem' }"
         :enable-time-picker="false" :placeholder="'Seleccionar Fechas'" :clearable="true">
        </VueDatePicker>
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