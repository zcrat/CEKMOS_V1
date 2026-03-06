<script setup lang="ts">

const emit = defineEmits<{
  (e: 'click'): void
}>()

</script>
<template>                   
    <button class="p-2  bg-[--btnprimary] rounded-lg text-white active:shadow-[0_0_12px_3px_var(--btnprimaryshadow)]"  @click="()=>{emit('click')}" ><font-awesome-icon icon="fa-solid fa-plus" class="pr-2"/>Nuevo</button>
</template>