<script setup lang="ts">
import Search from '@/components/Zcrat/Inputs/Search.vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import Table from '@/components/Zcrat/Elements/Table.vue'
import Dropdown from '@/components/Zcrat/Elements/DropdownWraper.vue'
import axios from 'axios'
import {ref} from 'vue'
import { UsersTable} from '@/utils/interfaces/users';
import Button  from "@/components/Zcrat/Inputs/Button.vue";
import ChangePermissionsUser from '@/components/Zcrat/modals/ChangePermissionsUser.vue'
import ChangeModulosServicio from '@/components/Zcrat/modals/ChangeModulosServicio.vue'
import BasicModal from '@/components/Zcrat/modals/BasicModal.vue'
import MyBasicToast from '@/utils/ToastNotificationBasic'

const rows = ref<UsersTable[]>([])
const loading = ref<boolean>(false)
const ModalExampleShoW = ref(false)
const ModalEditModuls = ref(false)
const openconfirmation = ref(false)
const iduser = ref<number | null>(null)

console.log("Componente cargado");


const GetElements=async ()=>{
    try {
        loading.value=true;
        const response = await axios.get(route('getusers'))
        rows.value=response.data.elements;
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
const DeleteUser=async ()=>{
    try {
        await axios.post(route('delete.user'),{id:iduser})
        MyBasicToast.success('Eliminado Correctamente')
        GetElements()
    } catch (error : any) {
        if (error.response?.status) {
            MyBasicToast.error(error.response.data.message || 'Error del servidor')
        } else {
        console.error('Error:', error)
        }
    }
}
GetElements() // ✅ ahora inject ya existe

</script>

<template>
    <AppLayout title="Usuarios" description="Bienvenido al sistema CEKMOS" :loanding="loading">
        <template #filtering>
            <div class="flex flex-row justify-start py-4  w-full">
                <Search Classdiv="sm:w-[20rem] w-full"/>
            </div>
        </template>
        <template #content>
            <Table  :titles="[
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
                                props: {text:'Editar Roles Y Permisos', onClick:()=>{iduser = row.id; ModalExampleShoW = true},hiddenclases:true,classname:'w-full text-center p-2 '}
                                },
                                {
                                element: Button,
                                props: {text:'Editar Modulos Visibles', onClick:()=>{iduser = row.id; ModalEditModuls = true},hiddenclases:true,classname:'w-full text-center p-2 '}
                                },
                                {
                                element: Button,
                                props: {text:'Eliminar', onClick:()=>{iduser = row.id; openconfirmation = true},hiddenclases:true, classname:'w-full text-center p-2 '}
                                },
                            ]
                        },classname:'items-center flex justify-center'
                        }
                                            ]
                }})" 
                
                classname="tabla"></Table>
        </template>
    </AppLayout>
    <ChangePermissionsUser v-model:show="ModalExampleShoW"  :id="iduser" />
    <ChangeModulosServicio v-model:show="ModalEditModuls"  :id="iduser" />
    <BasicModal v-model:modelValue="openconfirmation" :buttonconfirm="{text:'Si, Eliminar',onClick:()=>{DeleteUser(),openconfirmation=false},classname:'bg-red-600 font-bold text-white'}" >
        <h2 class="text-center text-lg">¿Realmente Deseas Eliminar al Usuario?</h2>
    </BasicModal>
</template>
