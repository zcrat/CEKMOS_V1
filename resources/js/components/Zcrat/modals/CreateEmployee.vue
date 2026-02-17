<!-- ModalExample.vue -->
<script setup lang="ts">

import InputBasic from '../Inputs/form/InputBasic.vue'
import Textarea from '../Inputs/form/Textarea.vue'
import BaseModal from '@/components/Zcrat/modals/BasicModal.vue'
import Create from '@/services/employee/create'
import { ref,reactive,computed,watch} from 'vue' 
import Subtitle from '@/components/Zcrat/Elements/Subtitle.vue';
import {type FormEmployee, type option} from '@/types/generales'
import {type buttonconfirmed} from '@/types/modals'
import Select2 from '@/components/Zcrat/Elements/ZDSelect.vue';

const props = defineProps<{show: boolean,close:()=>void}>()
const regimen_fiscal=ref<option|undefined>(undefined)
const Employee = reactive<FormEmployee>({
  name:'',
  paterno:'',
  materno:'',
  curp:'',
  rfc:'',
  regimen_fiscal:'',
  domicilio_fiscal:''
});
const buttonconfirm=computed<buttonconfirmed>(()=>{ 
  return {
    text:'Crear Empleado',
    classname:'bg-blue-600 text-white',
    onClick:()=>{
      Create(Employee).then((res)=>{
        if(res.status){
          props.close
          window.location.href=route('Presupuesto.Editar',{presupuesto:res.data.id});
        }else{
          if(res.code===422){
            console.error('Error de validacion al crear el presupuesto');
          }else if(res.code!=0){
            console.error('Error inesperado al crear el presupuesto');
          }else{
            console.error('Error de conexion al crear el presupuesto');
          }
        }
      });
    },
    disabled:Object.entries(Employee)
    .filter(([key]) => !['materno'].includes(key))
    .some(([_,value]) => value === null || value === '')}})


</script>

<template>

  <BaseModal 
    modaltitle="Nuevo Empleado" 
    :position="'center'" 
    :close="props.close" 
    :modelValue="props.show" 
    :buttonconfirm="buttonconfirm" >
    <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-2" >
      <InputBasic id="name" label="Nombre" type="text" v-model="Employee.name" />
      <InputBasic id="paterno" label="Paterno" type="text" v-model="Employee.paterno" />
      <InputBasic id="materno" label="Materno" type="text" v-model="Employee.materno" />
      <InputBasic id="curp" label="CURP" type="text" v-model="Employee.curp" :OnBlur="()=>{
        console.log(Employee.rfc)
        console.log(Employee.curp)
        if(!Employee.rfc  && Employee.curp !== ''){
          Employee.rfc=Employee.curp.substring(0,10);
        }
      }"/>
      <InputBasic id="rfc" label="RFC" type="text" v-model="Employee.rfc" />
      <Select2 :new_option="regimen_fiscal" 
        label="Regimen Fiscal" 
        endpoint="Select2.Regimenes.Fiscales" 
        v-model="Employee.regimen_fiscal" 
        id="regimen_fiscal" 
        placeholder="Buscar Regimen"
      />
      <Textarea id="domicilio_fiscal" class="sm:col-span-2 lg:col-span-3" label="Domicilio Fiscal" v-model="Employee.domicilio_fiscal" placeholder="Escribe el domicilio fiscal aqui..." classname="h-24"/>
    </div>
  </BaseModal>
</template>
