<script setup lang="ts">
import AppLayout from '@/Layouts/AppLayout.vue';
import { usePage } from '@inertiajs/vue3'
import { ref} from 'vue'
import Button  from "@/components/Zcrat/Inputs/Button.vue";
import Search from '@/components/Zcrat/Inputs/Search.vue';

const { props } = usePage()
const modulo = props.modulo
const prefacturasactive = false;
const loading = ref<boolean>(false);
 console.log("Modulo actual:", modulo);
</script>

<template>
    <AppLayout title="Presupuestos" description="Bienvenido al sistema CEKMOS" :loanding="loading">
        <template #header>
                <Button text="Nueva" />
                <Button text="Prefacturas" :classname="prefacturasactive?'bg-red-700' : 'bg-blue-700'"/>
        </template>
        <template #filtering>
                <Search Classdiv="sm:w-[20rem] w-full"/>
        </template>
        <template #content>
        </template>
    </AppLayout>
</template>
