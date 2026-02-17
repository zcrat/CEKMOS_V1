<!-- Vista que usa ModalExample.vue -->
<script setup lang="ts">
import Dropdown from '@/components/Zcrat/Elements/DropdownWraper.vue'
import {DataColumn} from '@/types/tablecomponent'
import {ref,} from 'vue'
import { Employee} from '@/types/users';
import Button  from "@/components/Zcrat/Inputs/Button.vue";
import BasicModal from '@/components/Zcrat/modals/BasicModal.vue'
import TableWithPagination from '@/components/Zcrat/TableWithPagination.vue';
import CreateEmployee from '@/components/Zcrat/modals/CreateEmployee.vue'

const ModalView = ref<number|null>(null)
const iduser = ref<number | null>(null)

const CraeteOptions=(row:Employee):DataColumn=>{

  return {
    element:Dropdown,
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
  <TableWithPagination
    titlePage="Empleados"
    :Actions="CraeteOptions"
    :titles="[
              'Nombre',
              'Paterno',
              'Materno',
              'CURP',
              'RFC',
              'Regimen Fiscal',
              'Domicilio Fiscal'
            ]"
    :keys="[
          'name',
          'lastname1',
          'lastname2',
          'curp',
          'rfc',
          'regimen_fiscal',
          'domicilio_fiscal',
    ]"
    api="employees.read"
  >
    
    <template #filtering1>
      <Button
        text="nuevo"
        icon="fa-solid fa-circle-plus"
        :onClick="() => (ModalView = 1)"
      />
    </template>
  </TableWithPagination>
    <CreateEmployee
      :show="ModalView===1"
      :close="()=>{ModalView=null}"
    />
    <BasicModal 
      :modelValue="ModalView===0" 
      :close="()=>{ModalView=null}"
      :buttonconfirm="{
        text:'Si, Eliminar',
        onClick:()=>{if(ModalView){
          ModalView+=1;
        }},
        classname:'bg-red-600 font-bold text-white'}" >
        <h2 class="text-center text-lg">¿Realmente Deseas Eliminar al Trabajador?</h2>
    </BasicModal>
</template>
