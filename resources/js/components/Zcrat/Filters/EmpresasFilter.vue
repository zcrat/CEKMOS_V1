<script setup lang="ts">
import ZDRemoteSelect from '@/components/Zcrat/Elements/ZDRemoteSelect.vue';
import type { option } from '@/types/generales';

const empresa = defineModel<option | null>({ default: null });
</script>

<template>
    <div class="sm:min-w-[12rem] w-full sm:w-auto">
        <ZDRemoteSelect v-model="empresa" endpoint="select2.empresas" label="Empresa" placeholder="Seleccionar empresa" class-div="w-full" />
    </div>
</template>
