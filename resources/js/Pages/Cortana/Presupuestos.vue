<script setup lang="ts">
import AppLayout from '@/Layouts/AppLayout.vue';
import { usePage } from '@inertiajs/vue3'
import { ref} from 'vue'
import Search from '@/components/Zcrat/Inputs/Search.vue';
import DropdownWrapper from '@/components/Zcrat/Elements/DropdownWraper.vue'
import Button from '@/components/Zcrat/Inputs/Button.vue';
import estatusfilter from '@/components/Zcrat/Filters/estatusfilter.vue';
import Datapicker from '@/components/Zcrat/Elements/ZDDataPicker.vue'
import Filterdates from '@/components/Zcrat/Filters/filterdates.vue';
import ModulosFilter from '@/components/Zcrat/Filters/ModulosFilter.vue';
import empresasselect from '@/components/Zcrat/Filters/empresasselect.vue'
interface ListadoModulos{
    id:number,
    label:string
}
const { props } = usePage()
const modulo = props.modulo
const prefacturasactive = false;
const loading = ref<boolean>(false);

const Listado  = ref<ListadoModulos[]>([{id:1,label:'CFE 2025 MORELIA GASOLINA'},{id:2,label:'CFE 2025 MORELIA DiSEL'},{id:2,label:'CFE 2025 BAJIO DiSEL'}])
 console.log("Modulo actual:", modulo);
</script>

<template>
    <AppLayout title="Presupuestos" description="Bienvenido al sistema CEKMOS" :loanding="loading">
        <template #header>
                <Button text="Exportar" />
                <Button text="Nueva" />
                <Button text="Prefacturas" :classname="prefacturasactive?'bg-red-700' : 'bg-blue-700'"/>
        </template>
        <template #filtering>
                <Search Classdiv="sm:w-[30rem] w-full" placeholder="Buscar Por Folio, PLacas, Economico o Order De Servicio" />
                <div class="flex gap-2 items-end">
                    <estatusfilter />
                    <ModulosFilter :Listado="Listado" />
                    <Datapicker />
                </div>
                <empresasselect/>
                
        </template>
        <template #content>
        </template>
    </AppLayout>
</template>
