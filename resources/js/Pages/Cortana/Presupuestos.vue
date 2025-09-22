<script setup lang="ts">
import AppLayout from '@/Layouts/AppLayout.vue';
import { usePage } from '@inertiajs/vue3'
import { ref,computed} from 'vue'
import Search from '@/components/Zcrat/Inputs/Search.vue';
import Table from '@/components/Zcrat/Elements/Table.vue'
import Dropdown from '@/components/Zcrat/Elements/DropdownWraper.vue'
import Button from '@/components/Zcrat/Inputs/Button.vue';
import estatusfilter from '@/components/Zcrat/Filters/estatusfilter.vue';
import Datapicker from '@/components/Zcrat/Elements/ZDDataPicker.vue';
import ModulosFilter from '@/components/Zcrat/Filters/ModulosFilter.vue';
import empresasselect from '@/components/Zcrat/Filters/empresasselect.vue'
import { modulosorden,presupuestos} from '@/utils/interfaces/generales';
import Pagination from '@/components/Zcrat/Filters/pagination.vue';
import axios from 'axios'
import MyBasicToast from '@/utils/ToastNotificationBasic'

const currentPage=ref<number>(1)
const itemsPerPage=ref<number>(10)
const totalPages=ref<number>(0)
const totalItems=ref<number>(0)
const items=ref<presupuestos[]>([])
const prefacturasactive = false;
const loading = ref<boolean>(false);
const message_empty=ref<string>('No Hay Presupuestos Para Mostrar')
const Listado  = ref<modulosorden[]>([{id:1,descripcion:'CFE 2025 MORELIA GASOLINA'},{id:2,descripcion:'CFE 2025 MORELIA DiSEL'},{id:2,descripcion:'CFE 2025 BAJIO DiSEL'}])

const SearchData=async (currentPage:number,itemsPerPage:number)=>{
    try {
        loading.value=true;
        const response = await axios.get(route('Cortana.Presupuesto.Items'),{params:{currentPage,itemsPerPage}})
        items.value=response.data.items;
    } catch (error : any) {
        if (error.response?.status === 500) {
            MyBasicToast.error(error.response.data.message || 'Error del servidor')
        } else {
        console.error('Error:', error)
        }
    }finally{
        loading.value=false
    }
}
SearchData(currentPage.value,itemsPerPage.value);

</script>

<template>
    <AppLayout title="Presupuestos" :loading="loading">
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
            <Pagination :onSearch="SearchData" :currentPage="currentPage" :itemsPerPage="itemsPerPage" :totalPages="totalPages" :totalItems="totalItems"/>
            <Table v-if="items.length>=1" :titles="[
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
                :rows="items.map(function(row){return {
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
                
                    classname="tabla mt-2">
            </Table>
            <div  class="flex-1 justify-center items-center flex" v-else>
                <span class="text-[1.5rem]">{{ message_empty }}</span>
            </div>
        </template>
    </AppLayout>
</template>
