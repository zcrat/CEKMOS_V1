<script setup lang="ts">
import ZDRemoteSelect from '@/components/Zcrat/Elements/ZDRemoteSelect.vue';
import { useAuth } from '@/composables/useAuth';
import type { option } from '@/types/generales';

const usuario = defineModel<option | null>({ default: null });
const { can } = useAuth();
const canFilter = can('ver_ordenes_servicio_todos');
</script>

<template>
    <div v-if="canFilter" class="min-w-[12rem]">
        <ZDRemoteSelect
            v-model="usuario"
            endpoint="select.usuarios.asignados"
            label="Usuario asignado"
            placeholder="Seleccionar usuario"
            classDiv="w-full"
        />
    </div>
</template>
