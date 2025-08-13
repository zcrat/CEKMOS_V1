<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { usePage } from '@inertiajs/vue3'

const { props } = usePage()
const modulo = props.modulo

</script>

<template>
    <AppLayout title="Presupuestos" description="Bienvenido al sistema CEKMOS">
        <template #header>
        </template>

        <div class="h-full flex flex-col items-center justify-center">
        </div>
    </AppLayout>
</template>
