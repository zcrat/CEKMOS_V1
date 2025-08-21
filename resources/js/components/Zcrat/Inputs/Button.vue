<script setup lang="ts">
import { defineProps } from 'vue'

const props=withDefaults(defineProps<{
  classname?: string
  hiddenclases?:boolean
  text: string
  icon?: string
  onClick?: (event: MouseEvent) => void
}>(),{
  hiddenclases: false
})

</script>
<template>
     <button :class="[hiddenclases?'':'flex flex-row gap-2 p-2 justify-center items-center border-2 rounded-lg bg-green-700 capitalize text-white ',classname]" @click="props.onClick">
        <font-awesome-icon  v-if="icon" :icon="icon" />
        <span>
            {{text}}
        </span>
     </button>
</template>