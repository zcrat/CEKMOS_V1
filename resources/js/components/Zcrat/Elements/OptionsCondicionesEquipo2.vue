<script setup lang="ts">

  withDefaults(defineProps<{
    label?:string;
    clases?:string
    claseslabel?:string
    toggle?:boolean
    invertClases?:boolean
    selected:string
    changeSelected:(val:string)=>void
  }>(), {
    invertClases: false,
    clases: 'flex w-fit flex-col',
    claseslabel: 'text-wrap break-all',
  })
</script>
<template>
    <div :class="clases">
        <h2 class="text-wrap break-all font-semibold" v-if="label">{{ label }}</h2>
        <div class="inline-flex gap-1">
          <button @click="changeSelected((selected ==='17' && toggle) ? '' :'17')" :class="['border-2 border-gray-00 px-2 rounded-md font-medium',selected==='17' ? (invertClases ? 'ButtonOptionsEquipo2' : 'ButtonOptionsEquipo1') : '']">D</button>
          <button @click="changeSelected((selected ==='15' && toggle) ? '' :'15')" :class="['border-2 border-gray-00 px-2 rounded-md font-medium',selected==='15' ? (invertClases ? 'ButtonOptionsEquipo2' : 'ButtonOptionsEquipo1') : '']">O</button>
          <button @click="changeSelected((selected ==='16' && toggle) ? '' :'16')" :class="['border-2 border-gray-00 px-2 rounded-md font-medium',selected==='16' ? (invertClases ? 'ButtonOptionsEquipo2' : 'ButtonOptionsEquipo1') : '']">F</button>
          <button @click="changeSelected((selected ==='18' && toggle) ? '' :'18')" :class="['border-2 border-gray-00 px-2 rounded-md font-medium',selected==='18' ? (invertClases ? 'ButtonOptionsEquipo2' : 'ButtonOptionsEquipo1') : '']">R</button>
          <button @click="changeSelected((selected ==='19' && toggle) ? '' :'19')" :class="['border-2 border-gray-00 px-2 rounded-md font-medium',selected==='19' ? (invertClases ? 'ButtonOptionsEquipo2' : 'ButtonOptionsEquipo1') : '']">NA</button>
          <button @click="changeSelected((selected ==='14' && toggle) ? '' :'14')" :class="['border-2 border-gray-00 px-2 rounded-md font-medium',selected==='14' ? (invertClases ? 'ButtonOptionsEquipo2' : 'ButtonOptionsEquipo1') : '']"><font-awesome-icon icon="fa-solid fa-check"></font-awesome-icon></button>
        </div>
    </div>
</template> 