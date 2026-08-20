<script setup lang="ts">
import VueDatePicker from '@vuepic/vue-datepicker';
import '@vuepic/vue-datepicker/dist/main.css'
import { watch } from 'vue';
import ZDListErrors from './ZDListErrors.vue';
import ZDIconError from './ZDIconError.vue';

const date = defineModel<Date | Date[] | null>();
const props = withDefaults(defineProps<{
  label?: string
  clearable?:boolean
  time?:boolean
  range?:boolean
  class?:string
  errors?: string[]
  DeleteErrors?: ()=>void
  disabled?:boolean
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
    <div :class="['flex flex-col',props.class??'w-[13rem]']">
      <label for="" v-if="props.label"><ZDIconError :errors="props.errors" hidden-absolute/> {{props.label }}</label>
      <div :class="['border-gray-500 border rounded-md',props.errors && props.errors.length > 0 ? 'inputerror':'', disabled ? 'bg-gray-100 opacity-60 cursor-not-allowed' : '']">
        <VueDatePicker 
        v-model="date" 
        :range="range?{ partialRange: false }:false" 
        :locale="'es'"
        auto-apply
        :ui="{ input:'!py-2 inputfocus' }"
        :enable-time-picker="time" :placeholder="'Seleccionar Fechas'" :clearable="clearable" :disabled="disabled">
        </VueDatePicker>
      </div>
      <ZDListErrors :errors="props.errors"/>
    </div>
</template>
