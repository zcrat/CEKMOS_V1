<!-- Vista que usa ModalExample.vue -->
<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue'
import Search from '@/components/Zcrat/Inputs/Search.vue'
import ButtonComponent from '@/components/Zcrat/Inputs/Button.vue'
import Table from '@/components/Zcrat/Elements/Table.vue'
import Dropdown from '@/components/Zcrat/Elements/Dropdown.vue'
import {ref,reactive} from 'vue'
import { CajaMovimientosTable} from '@/types/admin';
import Button  from "@/components/Zcrat/Inputs/Button.vue";

import { useDebounce } from '@vueuse/core'
import Pagination from '@/components/Zcrat/Filters/pagination.vue';

const ModalExampleShoW = ref(false)
const items = ref<CajaMovimientosTable[]>([])

const ModalEditModuls = ref(false)
const openconfirmation = ref(false)
const search = ref<string>("")
const iduser = ref<number | null>(null)
const message_empty=ref<string>('No Hay Movimientos Que Mostrar')
const debouncedSearch = useDebounce(search, 500)
const params = reactive({ search: debouncedSearch });

</script>

<template>
  <AppLayout title="Caja Movimientos">
    <template #filtering>
         <div class="flex flex-row justify-start py-4 w-full">
                <ButtonComponent
                text="nuevo"
                icon="fa-solid fa-circle-plus"
                :onClick="() => (ModalExampleShoW = true)"
                />
                <Search Classdiv="sm:w-[20rem] w-full" v-model="search"/>
            </div>
    </template>
    <template #content>
        <Pagination 
            v-model="items"
            :api="'Admin.Caja.Read'"
            :params="params"
        />
        <Table v-if="items.length >= 1" :titles="[
                    {title:'#No',classname:'uppercase'},
                    {title:'Tipo',classname:'uppercase'},
                    {title:'Cantidad',classname:'uppercase'},
                    {title:'Descripcion',classname:'uppercase'},
                    {title:'User',classname:'uppercase'},
                    {title:'opciones',classname:'uppercase'}
                ]"
                :rows="items.map(function(row){return {
                    classname:'bg-grey-300',
                    columns:[
                        {element:row.id, classname:'capitalize'},
                        {element:row.tipo, classname:'uppercase'},
                        {element:row.monto},
                        {element:row.fecha+'', classname:'uppercase'},
                        {element: Dropdown,
                            props: {
                                triggerText: 'Opciones',
                                options: [
                                    {
                                    label: 'Editar',
                                    onClick:()=>{iduser = row.id; ModalEditModuls = true},
                                    },
                                    {
                                    label: 'Eliminar',
                                    onClick:()=>{iduser = row.id; openconfirmation = true},
                                    },
                                ]
                                ,contentClasses:{bg:'bg-gray-200'}
                            }
                        }
                    ]
                }})" 
                
        classname="tabla"></Table>
        <div  class="flex-1 justify-center items-center flex" v-else>
                <span class="text-[1.5rem]">{{ message_empty }}</span>
            </div>
        </template>
    </AppLayout>
</template>
