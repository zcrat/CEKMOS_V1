<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import {computed, reactive, ref, watch} from 'vue'
import Search from '@/components/Zcrat/Inputs/Search.vue';
import Table from '@/components/Zcrat/Elements/Table.vue'
import Dropdown from '@/components/Zcrat/Elements/DropdownWraper.vue'
import Button from '@/components/Zcrat/Inputs/Button.vue';
import MultiOptionFilter from '@/components/Zcrat/Filters/MultiOptionFilter.vue';
import Datapicker from '@/components/Zcrat/Elements/ZDDataPicker.vue';
import empresasselect from '@/components/Zcrat/Filters/empresasselect.vue'
import {RecepcionesVehiculares} from '@/types/generales';
import Pagination from '@/components/Zcrat/Filters/pagination.vue';
import OrdenServicio from '@/components/Zcrat/modals/OrdenServicio.vue';
import { OrderKeyProp } from '@/types/tablecomponent';

const currentPage=ref<number>(1)
const itemsPerPage=ref<number>(10)
const totalItems=ref<number>(0)
const items=ref<RecepcionesVehiculares[]>([])
const search = ref<string>('');
const empresa = ref<string|null>(null);
const estatus = ref<string[]>([]);
const modulos = ref<string[]>([]);
const loading = ref<boolean>(false);
const message_empty=ref<string>('No Hay Presupuestos Para Mostrar')
const ShowNuevo = ref<boolean>(false)
const orderBy=ref<null|OrderKeyProp>(null)

const params = computed(() => ({
  search: search.value,
  estatus: estatus.value,
  empresa: empresa.value,
  modulos: modulos.value
}))
</script>

<template>
    <AppLayout title="Recepciones Vehiculares" :loading="loading">
        <template #header>
                <Button text="Nueva" @click="ShowNuevo=true"  />
        </template>
        <template #filtering>
            <div class="flex gap-2  flex-row">
                <Search Classdiv="sm:w-[30rem] w-full" placeholder="Buscar Por Order De Servicio, PLacas o Economico" v-model="search"/>
                <empresasselect v-model="empresa" :canNew="false"/>
            </div>
                <div class="flex gap-2 items-end justify-between sm:justify-start">
                    <MultiOptionFilter v-model:selectedIds="estatus" api="select.status" :params="{'categoria_id':2}" label="Estatus"/>
                    <MultiOptionFilter v-model:selectedIds="modulos" api="select.modulos.disponibles.usuario" label="Modulos"/>
                    <Datapicker />
                </div>
                
        </template>
        <template #content>
            
            <Pagination api="Cortana.OrdenServicio.Items" 
            :params="params" 
            v-model:currentPage="currentPage" 
            v-model:itemsPerPage="itemsPerPage" 
            v-model:totalItems="totalItems"
            v-model:items="items"
            changesItems
            />
            <Table v-if="items.length>0" 
                v-model:OrderKey="orderBy"
                :titles="[
                    {title:'No. Orden',CanOrder:{'key':'orden',types:'desc'}},
                    {title:'No. Seguimiento'},
                    {title:'Ubicacion'},
                    {title:'Empresa'},
                    {title:'Vehiculo',subtittles:[
                        {title:'Economico'},
                        {title:'Placas'},
                        {title:'Marca'},
                        {title:'Modelo'},
                    ]},
                    {title:'Entrada'},
                    {title:'Estatus'},
                    {title:'opciones'},
                ]"
                :rows="items.map(function(row){return {
                    classname:'bg-grey-300',
                    columns:[
                        {element:row.orden, classname:'capitalize'},
                        {element:row.seguimiento, classname:'capitalize'},
                        {element:row.ubicacion, classname:'capitalize'},
                        {element:row.empresa, classname:'lowercase'},
                        {element:row.economico,classname:'uppercase'},
                        {element:row.placas,classname:'uppercase'},
                        {element:row.marca, classname:'uppercase'},
                        {element:row.modelo,classname:'uppercase'},
                        {element:row.creacion, classname:'uppercase'},
                        {element:row.estatus, classname:'uppercase'},
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
    <OrdenServicio v-model:show="ShowNuevo"  @close="()=>ShowNuevo=false"/>
</template>
