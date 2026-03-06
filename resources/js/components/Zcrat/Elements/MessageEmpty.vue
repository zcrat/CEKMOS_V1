<template>
    <div class="flex items-center flex-grow justify-center text-2xl">
        <H4>{{message ??'No Hay Resultados'}}</H4>
    </div>
</template>

<script setup lang="ts">
  const props = defineProps<{
    classname?: string
    message?:string
  }>()
</script>