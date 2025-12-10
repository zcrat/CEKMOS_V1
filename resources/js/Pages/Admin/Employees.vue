<!-- Vista que usa ModalExample.vue -->
<script setup lang="ts">
import Search from '@/components/Zcrat/Inputs/Search.vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import Table from '@/components/Zcrat/Elements/Table.vue'
import Dropdown from '@/components/Zcrat/Elements/DropdownWraper.vue'
import axios from 'axios'
import {ref,Component} from 'vue'
import { Employee} from '@/types/users';
import Button  from "@/components/Zcrat/Inputs/Button.vue";
import BasicModal from '@/components/Zcrat/modals/BasicModal.vue'
import MyBasicToast from '@/utils/ToastNotificationBasic'

const rows = ref<Employee[]>([])
const loading = ref<boolean>(false)
const ModalView = ref<number|null>(null)
const iduser = ref<number | null>(null)

const CraeteOptions=(row:Employee):Component=>{

  return {
    Dropdown,
    props: {
        father: {
            element: Button,
            props: {text:'Opciones'},
        },
        children: [
            {
              element: Button,
              props: {
                text:'Eliminar', 
                onClick:()=>{iduser.value = row.id; ModalView.value = 0},
                hiddenclases:true, 
                classname:'w-full text-center p-2 '
              }
            },
        ]
        ,contentClasses:['bg-gray-200']
    }
  }
}
</script>

<template>
  <AppLayout title="Empleados" description="Bienvenido al sistema CEKMOS" :loanding="loading">
        <template #filtering>
          <div class="flex flex-row justify-start py-4 w-full">
            <ButtonComponent
              text="nuevo"
              icon="fa-solid fa-circle-plus"
              :onClick="() => (ModalView = 1)"
            />
            <Search Classdiv="sm:w-[20rem] w-full" />
          </div>
        </template>
        <template #content>
            <Table  :titles="[
                    'Nombre',
                    'Paterno',
                    'Materno',
                    'CURP',
                    'RFC',
                    'Regimen Fiscal',
                    'Domicilio Fiscal'
                ]"
                :rows="rows.map(function(row){return {
                    classname:'bg-grey-300',
                    columns:[
                        row.name,
                        row.lastname1,
                        row.lastname2,
                        {element:row.curp,classname:'uppercase'},
                        {element:row.rfc,classname:'uppercase'},
                        row.regimen_fiscal,
                        row.domicilio_fiscal,
                        

                        {element: CraeteOptions(row)
                        }
                                            ]
                }})" 
                
                classname="tabla"></Table>
        </template>
    </AppLayout>

    <BasicModal 
      :modelValue="ModalView===0" 
      :close="()=>{ModalView=null}"
      :buttonconfirm="{
        text:'Si, Eliminar',
        onClick:()=>{ModalView=null},
        classname:'bg-red-600 font-bold text-white'}" >
        <h2 class="text-center text-lg">¿Realmente Deseas Eliminar al Trabajador?</h2>
    </BasicModal>
</template>
