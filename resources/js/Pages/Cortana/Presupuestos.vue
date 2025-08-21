<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { usePage } from '@inertiajs/vue3'
import Dropdown from '@/components/Dropdown.vue';
import DropdownLink from '@/components/DropdownLink.vue';
import { ImageOff, Import } from 'lucide-vue-next';
import Button  from "@/components/Zcrat/Inputs/Button.vue";
import { text } from '@fortawesome/fontawesome-svg-core';
const { props } = usePage()
const modulo = props.modulo
const prefacturasactive = false;

</script>

<template>
    <AppLayout title="Presupuestos" description="Bienvenido al sistema CEKMOS">
        <template #header>
            <div class="flex">
                <Button text="Nueva"/>
                <Button text="Prefacturas" :classname="prefacturasactive?'bg-red-700' : 'bg-blue-700'"/>
            </div>
        </template>

        <div class="h-full flex flex-col items-center justify-center">
        </div>
    </AppLayout>
</template>
