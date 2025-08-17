<script setup lang="ts">
import Search from '@/Components/Zcrat/Inputs/Search.vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import Table from '@/Components/Zcrat/Elements/Table.vue'
import Dropdown from '@/Components/Zcrat/Elements/DropdownWraper.vue'
import axios from 'axios'
import { ref } from 'vue'
import { UsersTable} from '@/utils/interfaces/users';
import Button  from "@/Components/Zcrat/Inputs/Button.vue";

const rows = ref<UsersTable[]>([])
const loanding = ref<boolean>(true)

const GetElements=async ()=>{
    try {
        loanding.value=true;
        const response = await axios.get(route('getusers'))
        rows.value=response.data.elements;
    } catch (error) {
        if (error.response?.status === 500) {
        alert('Error del servidor')
        } else {
        console.error('Error:', error)
        }
    }finally{
        loanding.value=false
    }

}
GetElements();
</script>

<template>
    <AppLayout title="Usuarios" description="Bienvenido al sistema CEKMOS">
        <template #header>
        </template>

        <div class="h-full flex flex-col items-center justify-start sm:px-4">
            <div class="flex flex-row justify-start py-4  w-full">
                <Search Classdiv="sm:w-[20rem] w-full"/>
            </div>
            <div class="flex w-full ">
                <Table :titles="[
                        {title:'nombre',classname:'uppercase'},
                        {title:'correo',classname:'uppercase'},
                        {title:'verificado',classname:'uppercase'},
                        {title:'creado',classname:'uppercase'},
                        {title:'opciones',classname:'uppercase'}
                    ]"
                    :rows="rows.map(function(row){return {
                        classname:'',
                        columns:[
                            {element:row.name},
                            {element:row.email},
                            {element:row.verified ? 'Verificado':'Sin Verificar'},
                            {element:row.date+''},
                            {element: Dropdown,
                            props: {
                                father: {
                                    element: Button,
                                    props: {text:'Opciones'},
                                },
                                children: [
                                    {
                                    element: Button,
                                    props: {text:'Editar', onClick:GetElements,hiddenclases:true, classname:'w-full text-center p-2 '}
                                    },
                                    {
                                    element: Button,
                                    props: {text:'Eliminar', onClick:GetElements,hiddenclases:true,classname:'w-full text-center p-2 '}
                                    },
                                    
                                ]
                                }
                            }
                                                ]
                    }})" 
                    
                    classname="border-solid border-[1rem]"></Table>
            </div>
        </div>
    </AppLayout>
</template>
