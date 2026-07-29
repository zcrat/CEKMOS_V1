<script setup lang="ts">
import ZDRemoteSelect from '@/components/Zcrat/Elements/ZDRemoteSelect.vue';
import type { option } from '@/types/generales';

const empresa = defineModel<option | null>({ default: null });
</script>

<template>
    <div class="min-w-[12rem]">
        <ZDRemoteSelect v-model="empresa" endpoint="select2.empresas" label="Empresa" placeholder="Seleccionar empresa" class-div="w-full" />
    </div>
</template>
