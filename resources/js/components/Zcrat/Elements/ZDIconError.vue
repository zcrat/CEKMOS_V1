<script setup lang="ts">
const props = defineProps<{
  position?: 'end-0' | 'start-0'
  errors?:string[]
  hiddenAbsolute?:boolean
}>();
</script>

<template>
    <font-awesome-icon
    v-if="props.errors && props.errors.length > 0" 
      icon="fa-solid fa-circle-exclamation"
      :class="['text-red-500  text-[1.25em]',hiddenAbsolute ? '' :'absolute px-2 top-[50%] transform -translate-y-[50%]',position ??'start-0']"
    />
</template>