<script setup lang="ts">
  withDefaults(defineProps<{
    label:string;
    clases?:string
    claseslabel?:string
  }>(), {
    clases: 'flex w-fit flex-col',
    claseslabel: 'text-wrap break-all',
  })
  const selected = defineModel<string | null>()
</script>
<template>
    <div :class="clases">
        <h2 class="text-wrap break-all font-semibold">{{ label }}</h2>
        <div class="inline-flex gap-1">
          <button @click="selected='17'" :class="['border-2 border-gray-00 px-2 rounded-md font-medium',selected==='17' ? 'bg-[--micolor] font-semibold text-white':'']">D</button>
          <button @click="selected='15'" :class="['border-2 border-gray-00 px-2 rounded-md font-medium',selected==='15' ? 'bg-[--micolor] font-semibold text-white':'']">O</button>
          <button @click="selected='16'" :class="['border-2 border-gray-00 px-2 rounded-md font-medium',selected==='16' ? 'bg-[--micolor] font-semibold text-white':'']">F</button>
          <button @click="selected='18'" :class="['border-2 border-gray-00 px-2 rounded-md font-medium',selected==='18' ? 'bg-[--micolor] font-semibold text-white':'']">R</button>
          <button @click="selected='19'" :class="['border-2 border-gray-00 px-2 rounded-md font-medium',selected==='19' ? 'bg-[--micolor] font-semibold text-white':'']">NA</button>
          <button @click="selected='14'" :class="['border-2 border-gray-00 px-2 rounded-md font-medium',selected==='14' ? 'bg-[--micolor] font-semibold text-white':'']"><font-awesome-icon icon="fa-solid fa-check"></font-awesome-icon></button>
        </div>
    </div>
</template> 