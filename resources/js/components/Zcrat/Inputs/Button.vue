<script setup lang="ts">
import { computed } from 'vue';


const props=withDefaults(defineProps<{
  classname?: string
  disabled?:boolean
  hiddenclases?:boolean
  text?: string
  icon?: string
  title?: string
  ariaLabel?: string
  size?:'normal'|'compact'|'icon'
  type?:'new'|'delete'|'save'|'secondary'|'next'|'back'|'module'
}>(),{
  hiddenclases: false,
  type:'new',
  size:'normal'
})

const classbuttons={
  'new':' bg-green-700  text-white',
  'delete':' bg-red-700  text-white',
  'save':' bg-[--micolor]  text-white',
  'secondary':' bg-gray-500  text-white',
  'next':' border-emerald-300 bg-emerald-50 text-emerald-700 hover:border-emerald-400 hover:bg-emerald-100',
  'back':' border-amber-300 bg-amber-50 text-amber-800 hover:border-amber-400 hover:bg-amber-100',
  'module':' border-slate-300 bg-white text-slate-600 hover:border-slate-400 hover:bg-slate-100 hover:text-slate-900',
}
const classsizes = {
  'normal':'gap-2 border-2 rounded-lg p-2',
  'compact':'gap-1.5 border rounded-md px-3 py-1.5 text-xs font-semibold shadow-sm',
  'icon':'h-8 w-8 border rounded-md text-sm shadow-sm',
}
const classbutton =  computed(() =>
  'flex flex-row justify-center items-center capitalize transition-colors disabled:cursor-not-allowed disabled:opacity-60 '+
  classsizes[props.size]+' '+classbuttons[props.type]
);
const directionalIcon = computed(() => ({
  next: 'fa-solid fa-arrow-right',
  back: 'fa-solid fa-arrow-left',
}[props.type as 'next' | 'back']))
</script>
<template>
     <button
        :class="[hiddenclases?'':classbutton,classname]"
        :disabled="disabled"
        :title="title"
        :aria-label="ariaLabel ?? title"
     >
        <font-awesome-icon v-if="type === 'back' && !icon" :icon="directionalIcon" />
        <font-awesome-icon v-if="icon" :icon="icon" />
        <span v-if="text">
            {{text}}
        </span>
        <font-awesome-icon v-if="type === 'next' && !icon" :icon="directionalIcon" />
     </button>
</template>
