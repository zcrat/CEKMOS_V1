<script setup lang="ts">
import MultiOptionFilter from '@/components/Zcrat/Filters/MultiOptionFilter.vue';

const talleres = defineModel<(number | string)[]>({ default: [] });
</script>

<template>
    <MultiOptionFilter v-model:selectedIds="talleres" api="select2.talleres" label="Talleres" />
</template>
