<script setup lang="ts">
import VueDatePicker from '@vuepic/vue-datepicker';
import '@vuepic/vue-datepicker/dist/main.css'
import { ref, watch } from 'vue';
import ZDListErrors from './ZDListErrors.vue';
import ZDIconError from './ZDIconError.vue';

const date = defineModel<Date>();
const props = withDefaults(defineProps<{
  label?: string
  clearable?:boolean
  time?:boolean
  range?:boolean
  class?:string
  errors?: string[]
  DeleteErrors?: ()=>void
}>(),{
  clearable:true,
  time:false,
  range:true,
})
  watch(date, () => {
    props.DeleteErrors?.();
  }) 
</script>
<template>
    <div :class="['h-full flex flex-col',props.class??'w-[16rem]']">
      <label for="" v-if="props.label"><ZDIconError :errors="props.errors" hidden-absolute/> {{props.label }}</label>
      <div :class="['border-gray-500 border rounded-md',props.errors && props.errors.length > 0 ? 'inputerror':'']">
        <VueDatePicker 
        v-model="date" 
        :range="range?{ partialRange: false }:false" 
        :locale="'es'"
        auto-apply
        :ui="{ input:'py-8 inputfocus' }"
        :style="{ '--dp-input-padding': '0.5rem'}"
        :enable-time-picker="time" :placeholder="'Seleccionar Fechas'" :clearable="clearable">
        </VueDatePicker>
      </div>
      <ZDListErrors :errors="props.errors"/>
    </div>
</template>