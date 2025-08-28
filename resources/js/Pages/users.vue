<script setup lang="ts">
import Search from '@/components/Zcrat/Inputs/Search.vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import Table from '@/components/Zcrat/Elements/Table.vue'
import Dropdown from '@/components/Zcrat/Elements/DropdownWraper.vue'
import axios from 'axios'
import { ref, onMounted,getCurrentInstance} from 'vue'
import { UsersTable} from '@/utils/interfaces/users';
import Button  from "@/components/Zcrat/Inputs/Button.vue";
import Loanding from '@/components/Zcrat/Elements/Loanding.vue';
import ChangePermissionsUser from '@/components/Zcrat/modals/ChangePermissionsUser.vue'


const rows = ref<UsersTable[]>([])
const loanding = ref<boolean>(true)
const ModalExampleShoW = ref(false)
const iduser = ref<number | null>(null)

console.log("Componente cargado");


const GetElements=async ()=>{
    try {
        loanding.value=true;
        const response = await axios.get(route('getusers'))
        rows.value=response.data.elements;
    } catch (error : any) {
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
                <Loanding v-if="loanding" text="Cargando Usuarios"/>
                <Table v-else :titles="[
                        {title:'nombre',classname:'uppercase'},
                        {title:'correo',classname:'uppercase'},
                        {title:'verificado',classname:'uppercase'},
                        {title:'creado',classname:'uppercase'},
                        {title:'opciones',classname:'uppercase'}
                    ]"
                    :rows="rows.map(function(row){return {
                        classname:'',
                        columns:[
                            {element:row.name, classname:'capitalize'},
                            {element:row.email, classname:'lowercase'},
                            {element:row.verified ? 'Verificado':'Sin Verificar',classname:'uppercase'},
                            {element:row.date+'', classname:'uppercase'},
                            {element: Dropdown,
                            props: {
                                father: {
                                    element: Button,
                                    props: {text:'Opciones'},
                                },
                                children: [
                                    {
                                    element: Button,
                                    props: {text:'Editar', onClick:()=>{iduser = row.id; ModalExampleShoW = true},hiddenclases:true,classname:'w-full text-center p-2 '}
                                    },
                                    {
                                    element: Button,
                                    props: {text:'Eliminar', onClick:GetElements,hiddenclases:true, classname:'w-full text-center p-2 '}
                                    },
                                ]
                            },classname:'items-center flex justify-center'
                            }
                                                ]
                    }})" 
                    
                    classname="tabla"></Table>
            </div>
        </div>
    </AppLayout>
    <ChangePermissionsUser v-model:show="ModalExampleShoW"  :id="iduser" />
</template>
