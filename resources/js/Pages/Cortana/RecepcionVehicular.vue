<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import {computed,ref, watch} from 'vue'
import Search from '@/components/Zcrat/Inputs/Search.vue';
import Table from '@/components/Zcrat/Elements/Table.vue'
import Dropdown from '@/components/Zcrat/Elements/Dropdown.vue'
import Button from '@/components/Zcrat/Inputs/Button.vue';
import MultiOptionFilter from '@/components/Zcrat/Filters/MultiOptionFilter.vue';
import Datapicker from '@/components/Zcrat/Elements/ZDDataPicker.vue';
import empresasselect from '@/components/Zcrat/Filters/empresasselect.vue'
import {RecepcionesVehiculares} from '@/types/generales';
import Pagination from '@/components/Zcrat/Filters/pagination.vue';
import OrdenServicio from '@/components/Zcrat/modals/OrdenServicio.vue';
import { OrderKeyProp } from '@/types/tablecomponent';
import { ToggleUploadFiles } from '@/utils/functions/ordenservicio';
import { useEcho } from '@laravel/echo-vue';
import PDFDemo from '@/components/Zcrat/modals/PDFDemo.vue';

const currentPage=ref<number>(1)
const itemsPerPage=ref<number>(10)
const totalItems=ref<number>(0)
const items=ref<RecepcionesVehiculares[]>([])
const search = ref<string>('');
const empresa = ref<string|null>(null);
const estatus = ref<string[]>([]);
const modulos = ref<string[]>([]);
const loading = ref<boolean>(true);
const message_empty=ref<string>('No Hay Recepciones Vehiculares Para Mostrar')
const statusParams = { categoria_id: 2 }
const orderBy=ref<null|OrderKeyProp>(null)
const ModalOrdenServicio = ref<InstanceType<typeof OrdenServicio> | null>(null);
const pdf = ref<InstanceType<typeof PDFDemo> | null>(null);
interface DaTaUpdateWebSocket extends Record<string,any> {
    id:number
}
const params = computed(() => ({
  search: search.value,
  estatus: estatus.value,
  empresa: empresa.value,
  modulos: modulos.value
}))


useEcho(
  `ordenes_servicio`, '.update', (data:DaTaUpdateWebSocket) => {
    console.log(data)
    items.value = items.value.map((item)=>{
        if(item.id === data.id){
            return {...item,...data}
        }
        return item
    })
  }
)
useEcho(
  `ordenes_servicio`, '.delete', (data:{id:number}) => {
    items.value = items.value.filter((item)=>item.id !== data.id)
  }
)


</script>

<template>
    <AppLayout title="Recepciones Vehiculares" :loading="loading" messageLoading="Cargando Recepciones Vehiculares">
        <template #header>
                <Button text="Nueva" @click="ModalOrdenServicio?.Open(null)"  />
        </template>
        <template #filtering>
            <div class="flex gap-2 flex-col lg:flex-row">
                <div class="flex gap-2 flex-row">
                    <Search Classdiv="sm:w-[25rem] w-full" placeholder="Buscar Por Order De Servicio, PLacas o Economico" v-model="search"/>
                    <empresasselect v-model="empresa" :canNew="false"/>
                </div>
                <div class="flex gap-2 items-end justify-between sm:justify-start">
                    <MultiOptionFilter v-model:selectedIds="estatus" api="select.status" :params="statusParams" label="Estatus"/>
                    <MultiOptionFilter v-model:selectedIds="modulos" api="select.modulos.disponibles.usuario" label="Modulos"/>
                    <Datapicker />
                </div>
            </div>
            <Pagination 
                api="RecepcionesVehiculares.Read" 
                :params="params" 
                v-model:currentPage="currentPage" 
                v-model:itemsPerPage="itemsPerPage" 
                v-model:totalItems="totalItems"
                v-model:items="items"
                v-model:loading="loading"
                changesItems
            />
        </template>
        <template #content>
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
                                triggerText:'Opciones',
                                options: [
                                    {
                                        label:'Editar Recepcion', 
                                        onClick:()=>{ModalOrdenServicio?.Open(row.id)},
                                        classname:['hover:text-gray-800']
                                    },
                                    {
                                        label:'Descargar PDF', 
                                        onClick:()=>{pdf?.Open(row.id)},
                                        classname:['hover:text-gray-800']
                                    },
                                    {
                                        label:((row.cambiar_archivos ? 'Desactivar ' : 'Activar ') + 'Cambios en los Archivos'), 
                                        onClick:()=>{ToggleUploadFiles({id:row.id,estatus:row.cambiar_archivos})},
                                        classname:['hover:text-gray-800']
                                    },
                                ]
                                ,
                                contentClasses:{
                                    bg:'bg-gray-300'
                                }
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
    <OrdenServicio ref="ModalOrdenServicio"/>
    <PDFDemo ref="pdf"/>
</template>
