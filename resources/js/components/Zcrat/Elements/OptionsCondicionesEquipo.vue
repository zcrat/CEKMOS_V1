<script setup lang="ts">
import { watch } from 'vue';
import ZDListErrors from './ZDListErrors.vue';
import ZDIconError from './ZDIconError.vue';


  const props = withDefaults(defineProps<{
    label?:string;
    divClass?:string
    labelClass?:string
    toggle?:boolean
    invertClases?:boolean
    errors?: string[]
    DeleteErrors?: ()=>void
  }>(), {
    invertClases: false,
    divClass: 'flex w-fit flex-col h-full',
    labelClass: 'text-wrap break-all font-semibold',
  })
  const selected = defineModel<string | null>()
  const changeSelected = (val : string) =>{
    selected.value= (selected.value === val && props.toggle) ? '' :val;
  }
  watch(selected, () => {
    props.DeleteErrors?.();
  })
</script>
<template>
    <div :class="divClass">
      <div class="">
        <h2 :class="labelClass" v-if="label"><ZDIconError :errors="props.errors" hidden-absolute/>{{ label }}</h2>
      </div>
      <div :class="['inline-flex gap-1 p-1',props.errors && props.errors.length > 0 ? 'inputerror':'']">
        <button @click="changeSelected('17')" :class="['ButtonCondicionesEquipo',selected==='17' ? (invertClases ? 'ButtonOptionsEquipo2' : 'ButtonOptionsEquipo1') : '']">D</button>
        <button @click="changeSelected('15')" :class="['ButtonCondicionesEquipo',selected==='15' ? (invertClases ? 'ButtonOptionsEquipo2' : 'ButtonOptionsEquipo1') : '']">O</button>
        <button @click="changeSelected('16')" :class="['ButtonCondicionesEquipo',selected==='16' ? (invertClases ? 'ButtonOptionsEquipo2' : 'ButtonOptionsEquipo1') : '']">F</button>
        <button @click="changeSelected('18')" :class="['ButtonCondicionesEquipo',selected==='18' ? (invertClases ? 'ButtonOptionsEquipo2' : 'ButtonOptionsEquipo1') : '']">R</button>
        <button @click="changeSelected('19')" :class="['ButtonCondicionesEquipo',selected==='19' ? (invertClases ? 'ButtonOptionsEquipo2' : 'ButtonOptionsEquipo1') : '']">NA</button>
        <button @click="changeSelected('14')" :class="['ButtonCondicionesEquipo',selected==='14' ? (invertClases ? 'ButtonOptionsEquipo2' : 'ButtonOptionsEquipo1') : '']"><font-awesome-icon icon="fa-solid fa-check"></font-awesome-icon></button>
      </div>
      <ZDListErrors :errors="errors"/>
    </div>
</template> 