<script setup lang="ts">
import MultiOptionFilter from '@/components/Zcrat/Filters/MultiOptionFilter.vue';

const modulos = defineModel<(number | string)[]>({ default: [] });
</script>

<template>
    <MultiOptionFilter v-model:selectedIds="modulos" api="select.modulos.disponibles.usuario" label="Módulos" />
</template>
