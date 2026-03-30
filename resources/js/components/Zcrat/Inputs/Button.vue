<script setup lang="ts">
import { computed } from 'vue';


const props=withDefaults(defineProps<{
  classname?: string
  disabled?:boolean
  hiddenclases?:boolean
  text?: string
  icon?: string
  type?:'new'|'delete'|'save'|'secondary'
}>(),{
  hiddenclases: false,
  type:'new'
})

const classbuttons={
  'new':' bg-green-700  text-white',
  'delete':' bg-red-700  text-white',
  'save':' bg-[--micolor]  text-white',
  'secondary':' bg-gray-500  text-white',
}
const classbutton =  computed(() => 'flex flex-row gap-2 p-2 justify-center items-center border-2 rounded-lg capitalize disabled:opacity-80 '+classbuttons[props.type]);
</script>
<template>
     <button :class="[hiddenclases?'':classbutton,classname]" :disabled="disabled" >
        <font-awesome-icon  v-if="icon" :icon="icon" />
        <span v-if="text">
            {{text}}
        </span>
     </button>
</template>