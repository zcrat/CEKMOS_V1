<script setup lang="ts">
import MultiOptionFilter from '@/components/Zcrat/Filters/MultiOptionFilter.vue';

const estatus = defineModel<(number | string)[]>({ default: [] });
</script>

<template>
    <MultiOptionFilter v-model:selectedIds="estatus" api="select.status" :params="{ categoria_id: 2 }" label="Estatus" />
</template>
