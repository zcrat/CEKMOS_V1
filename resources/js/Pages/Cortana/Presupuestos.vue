<script setup lang="ts">
import AppLayout from '@/Layouts/AppLayout.vue';
import { usePage } from '@inertiajs/vue3'
import { ref} from 'vue'
import Search from '@/components/Zcrat/Inputs/Search.vue';
import Table from '@/components/Zcrat/Elements/Table.vue'
import Dropdown from '@/components/Zcrat/Elements/DropdownWraper.vue'
import Button from '@/components/Zcrat/Inputs/Button.vue';
import estatusfilter from '@/components/Zcrat/Filters/estatusfilter.vue';
import Datapicker from '@/components/Zcrat/Elements/ZDDataPicker.vue';
import ModulosFilter from '@/components/Zcrat/Filters/ModulosFilter.vue';
import empresasselect from '@/components/Zcrat/Filters/empresasselect.vue'
import { modulosorden,presupuestos} from '@/utils/interfaces/generales';


const rows = ref<presupuestos[]>([])
const { props } = usePage()
const modulo = props.modulo
const prefacturasactive = false;
const loading = ref<boolean>(false);

const Listado  = ref<modulosorden[]>([{id:1,descripcion:'CFE 2025 MORELIA GASOLINA'},{id:2,descripcion:'CFE 2025 MORELIA DiSEL'},{id:2,descripcion:'CFE 2025 BAJIO DiSEL'}])
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
            <div class="flex gap-2  flex-row">
                <Search Classdiv="sm:w-[30rem] w-full" placeholder="Buscar Por Folio, PLacas, Economico o Order De Servicio" />
                <empresasselect/>
            </div>
                <div class="flex gap-2 items-end justify-between sm:justify-start">
                    <estatusfilter />
                    <ModulosFilter :Listado="Listado" />
                    <Datapicker />
                </div>
                
        </template>
        <template #content>
            <Table  :titles="[
                {title:'opciones',classname:'uppercase'},
                {title:'Folio',classname:'uppercase'},
                {title:'No. Orden',classname:'uppercase'},
                {title:'Empresa',classname:'uppercase'},
                {title:'Economico',classname:'uppercase'},
                {title:'Placas',classname:'uppercase'},
                {title:'Vin',classname:'uppercase'},
                {title:'Creacion',classname:'uppercase'},
                {title:'Estatus',classname:'uppercase'},
                ]"
                :rows="rows.map(function(row){return {
                    classname:'bg-grey-300',
                    columns:[
                        {element:row.folio, classname:'capitalize'},
                        {element:row.empresa, classname:'lowercase'},
                        {element:row.economico ? 'Verificado':'Sin Verificar',classname:'uppercase'},
                        {element:row.placas+'', classname:'uppercase'},
                        {element:row.vin+'', classname:'uppercase'},
                        {element:row.creacion+'', classname:'uppercase'},
                        {element:row.estatus+'', classname:'uppercase'},
                        {element: Dropdown,
                        props: {
                            father: {
                                element: Button,
                                props: {text:'Opciones'},
                            },
                            children: [
                                {
                                element: Button,
                                props: {text:'Editar Roles Y Permisos', onClick:()=>{console.log(row.id)},hiddenclases:true,classname:'w-full text-center p-2 hover:text-gray-500 text=black text-md'}
                                },
                            ]
                            ,contentClasses:['bg-gray-200']
                        }
                        }
                                            ]
                }})" 
                
                classname="tabla mt-2"></Table>
        </template>
    </AppLayout>
</template>
