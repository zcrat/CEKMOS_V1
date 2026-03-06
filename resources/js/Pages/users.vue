<script setup lang="ts">
import Search from '@/components/Zcrat/Inputs/Search.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import Table from '@/components/Zcrat/Elements/Table.vue'
import Dropdown from '@/components/Zcrat/Elements/DropdownWraper.vue'
import axios from 'axios'
import {onMounted, ref, watch} from 'vue'
import { UsersTable} from '@/types/users';
import Button  from "@/components/Zcrat/Inputs/Button.vue";
import ChangePermissionsUser from '@/components/Zcrat/modals/ChangePermissionsUser.vue'
import ChangeModulosServicio from '@/components/Zcrat/modals/ChangeModulosServicio.vue'
import BasicModal from '@/components/Zcrat/modals/BasicModal.vue'
import MyBasicToast from '@/utils/ToastNotificationBasic'
import { useEcho } from '@laravel/echo-vue';
import { OrderKeyProp } from '@/types/tablecomponent';
import Paginationv2,{paginationrefs} from '@/components/Zcrat/Filters/paginationv2.vue';
import GetElementsFuntion from '@/utils/functions/GetElements';
import MessageEmpty from '@/components/Zcrat/Elements/MessageEmpty.vue';
import ButtonNewElement from '@/components/Zcrat/Elements/ButtonNewElement.vue';
import UserRegister from '@/components/Zcrat/modals/UserRegister.vue';

interface DataEvent {
    message: string;
    tipo: number;
    id_user: number;
};

const rows = ref<UsersTable[]>([])
const loading = ref<boolean>(false)
const modalshow = ref<number>(0)
const ModalEditModuls = ref(false)
const openconfirmation = ref(false)
const OrderKey = ref<OrderKeyProp | null>(null)
const iduser = ref<number | null>(null)


const filters = ref<{
    search:string
}>({
    search:''
})
const pagination:paginationrefs = {
    currentPage:ref(1),
    itemsPerPage:ref(10),
    totalElements:ref(0)
}
const modalactive=ref<number>(0)

const {debouncedGetElements,GetElements} = GetElementsFuntion({loading,
    params:() => ({ order: OrderKey.value,
            search: filters.value.search,
            currentPage: pagination.currentPage.value,
            itemsPerPage: pagination.itemsPerPage.value
        }),rows,totalRows:pagination.totalElements,api:'getusers'})


useEcho(
  `UsersEvents`,
  '.Events',
  (data: DataEvent) => {
    if (data.tipo === 58) {
      rows.value = rows.value.filter(user => user.id !== data.id_user);
    }
  }
)
const DeleteUser=async ()=>{
    try {
        await axios.post(route('delete.user'),{id:iduser.value})
        MyBasicToast.success('Eliminado Correctamente')
    } catch (error : any) {
        if (error.response?.status) {
            MyBasicToast.error(error.response.data.message || 'Error del servidor')
        } else {
        console.error('Error:', error)
        }
    }
}
watch([OrderKey,() => filters.value.search,pagination.itemsPerPage], () => {
    console.log('cambio')
    if(pagination.currentPage.value != 1){
        pagination.currentPage.value = 1
    }else{
        debouncedGetElements()
    }
})
watch([pagination.currentPage], () => {
    console.log(pagination.currentPage.value)
    debouncedGetElements()
})
onMounted(()=>{
    GetElements()
})


</script>

<template>
    <UserRegister :show="modalactive == 1" @close="modalactive=0"/>
    <AppLayout title="Usuarios" description="Bienvenido al sistema CEKMOS" :loading="loading">
        <template #filtering>
            <div class="flex flex-row justify-start py-4 gap-2 w-full">
                <ButtonNewElement @click="()=>{modalactive=1}"/>
                <Search Classdiv="sm:w-[20rem] w-full"  v-model="filters.search"/>
            </div>
        </template>
        <template #content>
            <Paginationv2 v-model:currentPage="pagination.currentPage.value" v-model:itemsPerPage="pagination.itemsPerPage.value" :totalElements="pagination.totalElements.value"/>
            <Table  :titles="[
                    {title:'nombre',classname:'uppercase',CanOrder:{key:'name','types':'ambos'}},
                    {title:'correo',classname:'uppercase',CanOrder:{key:'email','types':'ambos'}},
                    {title:'verificado',classname:'uppercase'},
                    {title:'creado',classname:'uppercase',CanOrder:{key:'created_at','types':'ambos'}},
                    {title:'opciones',classname:'uppercase'}
                ]"
                v-if="pagination.totalElements.value > 0"
                v-model:OrderKey="OrderKey"
                :rows="rows.map(function(row){return {
                    classname:'bg-grey-300',
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
                                props: {text:'Editar Roles Y Permisos', onClick:()=>{iduser = row.id; modalshow = 2},hiddenclases:true,classname:'w-full text-center p-2 hover:text-gray-500 text=black text-md'}
                                },
                                {
                                element: Button,
                                props: {text:'Editar Modulos Visibles', onClick:()=>{iduser = row.id; modalshow = 3},hiddenclases:true,classname:'w-full text-center p-2 hover:text-gray-500 text=black text-md'}
                                },
                                {
                                element: Button,
                                props: {text:'Eliminar', onClick:()=>{iduser = row.id; modalshow = 1},hiddenclases:true, classname:'w-full text-center p-2 '}
                                },
                            ]
                            ,contentClasses:['bg-gray-200']
                        }
                        }
                                            ]
                }})" 
                
                classname="tabla"/>
                <MessageEmpty v-else/>
        </template>
    </AppLayout>
    <ChangePermissionsUser :show="modalshow === 2"  :id="iduser"  @close="()=>{modalshow=0}"/>
    <ChangeModulosServicio :show="modalshow === 3"  :id="iduser"  @close="()=>{modalshow=0}"/>
    <BasicModal :show="modalshow === 1 " :buttonconfirm="{text:'Si, Eliminar',onClick:()=>{DeleteUser(),openconfirmation=false},classname:'bg-red-600 font-bold text-white'} " @close="()=>{modalshow=0}">
        <h2 class="text-center text-lg">¿Realmente Deseas Eliminar al Usuario?</h2>
    </BasicModal>
</template>
