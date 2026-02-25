<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import {ref} from 'vue'
import Search from '@/components/Zcrat/Inputs/Search.vue';
import Table from '@/components/Zcrat/Elements/Table.vue'
import Dropdown from '@/components/Zcrat/Elements/DropdownWraper.vue'
import Button from '@/components/Zcrat/Inputs/Button.vue';
import MultiOptionFilter from '@/components/Zcrat/Filters/MultiOptionFilter.vue';
import Datapicker from '@/components/Zcrat/Elements/ZDDataPicker.vue';
import empresasselect from '@/components/Zcrat/Filters/empresasselect.vue'
import {presupuestos} from '@/types/generales';
import Pagination from '@/components/Zcrat/Filters/pagination.vue';
import Nuevo from '@/components/Zcrat/modals/CreatePresupuesto.vue';
import { OrderKeyProp } from '@/types/tablecomponent';
const currentPage=ref<number>(1)
const itemsPerPage=ref<number>(10)
const totalPages=ref<number>(0)
const totalItems=ref<number>(0)
const items=ref<presupuestos[]>([])
const search = ref<string>('');
const estatus = ref<string[]>([]);
const modulos = ref<string[]>([]);

const prefacturasactive = false;
const loading = ref<boolean>(false);
const message_empty=ref<string>('No Hay Presupuestos Para Mostrar')

const ShowNuevo = ref<boolean>(false)
const orderBy=ref<null|OrderKeyProp>(null)

</script>

<template>
    <AppLayout title="Presupuestos" :loading="loading">
        <template #header>
                <Button text="Exportar" />
                <Button text="Nueva" @click="ShowNuevo=true"  />
                <Button text="Prefacturas" :classname="prefacturasactive?'bg-red-700' : 'bg-blue-700'"/>
        </template>
        <template #filtering>
            <div class="flex gap-2  flex-row">
                <Search Classdiv="sm:w-[30rem] w-full" placeholder="Buscar Por Folio, PLacas, Economico o Order De Servicio" v-model="search"/>
                <empresasselect/>
            </div>
                <div class="flex gap-2 items-end justify-between sm:justify-start">
                    <MultiOptionFilter v-model:selectedIds="estatus" api="select.status" :params="{'categoria_id':2}" label="Estatus"/>
                    <MultiOptionFilter v-model:selectedIds="modulos" api="select.modulos.disponibles.usuario" label="Modulos"/>
                    <Datapicker />
                </div>
                
        </template>
        <template #content>
            <Pagination api="Cortana.Presupuesto.Items" :params="{'search':search,'estatus':estatus}" :currentPage="currentPage" :itemsPerPage="itemsPerPage" :totalPages="totalPages" :totalItems="totalItems"/>
            <Table v-if="items.length>=0" 
                v-model:OrderKey="orderBy"
                :titles="[
                    {title:'opciones',classname:'uppercase'},
                    {title:'Folio',classname:'uppercase',CanOrder:{'key':'folio',types:'ambos'}},
                    {title:'No. Orden',classname:'uppercase',CanOrder:{'key':'orden',types:'desc'}},
                    {title:'Empresa',classname:'uppercase',CanOrder:{'key':'empresa',types:'asc'}},
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
                        {element:row.orden, classname:'capitalize'},
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
    <Nuevo v-model:show="ShowNuevo" />
</template>
