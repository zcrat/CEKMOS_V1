<script setup lang="ts">
import { defineProps,computed  } from 'vue'

const props=withDefaults(defineProps<{
  isnew: boolean
  text: string
  classname?: string
  onClick?: (event: MouseEvent) => void
}>(),{
})

const clases = computed(() => props.isnew ? 'bg-green-700' : 'bg-red-400')
const icon   = computed(() => props.isnew ? 'fa-solid fa-plus' : 'fa-solid fa-trash-can')
</script>

<template>
     <div :class="['gap-2 flex flex-row', props.classname] ">
          <button :class="[clases, 'px-1 rounded']" @click="props.onClick">
               <font-awesome-icon :icon="icon"/>
          </button>
          <label class="text  break-words uppercase">{{props.text}}</label>
    </div>
</template>