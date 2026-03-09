<!-- ModalExample.vue -->
<script setup lang="ts">

import InputBasic from '../Inputs/form/InputBasic.vue'
import BaseModal from '@/components/Zcrat/modals/BasicModal.vue'
import { ref,reactive,computed, watch, watchEffect} from 'vue' 
import {type FormUser, type option} from '@/types/generales'
import {type buttonconfirmed} from '@/types/modals'
import axios from 'axios'
import MyBasicToast from '@/utils/ToastNotificationBasic'
import Loading from '../Elements/Loading.vue'

const props = defineProps<{show: boolean, userid?:number|null}>()
const emit = defineEmits<{
  (e: 'close'): void
}>()
const regimen_fiscal=ref<option|undefined>(undefined)
const cargando=ref<string|undefined>(undefined)
const UserForm = reactive<FormUser>({
  name:'',
  paterno:'',
  materno:'',
  email:'',
  password:'',
  password_confirmation:'',
  username:'',
  id:undefined
});
watch(() => props.userid, (val) => {
  UserForm.id=val
}, { immediate: true })

watchEffect(()=>{
  console.log(UserForm.id)
  console.log(props.show)
  console.log('ubocambio')
  if(UserForm.id && props.show){
    console.log('soli')

    Read()
  }else{
    UserForm.name='';
    UserForm.paterno='';
    UserForm.materno='';
    UserForm.email='';
    UserForm.password='';
    UserForm.password_confirmation='';
    UserForm.username='';
  }
})
const ErrorUserForm = ref<Record<string, string[]>>({});

const Create = async () => {
  try {
      ErrorUserForm.value={};
      const response = await axios.post(route(props.userid ? 'user.update': 'user.create'),{...UserForm})
      MyBasicToast.success(response.data.message)
      emit('close')
  } catch (error: any) {
      const status = error.response?.status ?? 0;
      const data = error.response?.data ?? {'message':"Error inesperado"};
      if (status === 422) {
        if( data.errors){
          const formattedErrors: Record<string, string[]> = {};
          Object.entries(data.errors).forEach(([field, messages]) => {
            formattedErrors[field] = (messages as string[]); // tomamos el primer mensaje
          });
          ErrorUserForm.value=formattedErrors;
        } else{
          MyBasicToast.error(data.message ?? 'Errores de Validacion')
        }
        
      }else{
        MyBasicToast.error(data.message??'Ocurrio Un Error Inesperado')
      }
  }
}
const Read = async () => {
  try {
    cargando.value='Cargando Datos Del Usuario';
    const response = await axios.get(route('user.read'),{params:{
      id:UserForm.id
    }})
    UserForm.name=response.data.datauser.name;
    UserForm.paterno=response.data.datauser.paterno;
    UserForm.materno=response.data.datauser.materno;
    UserForm.email=response.data.datauser.email;
    UserForm.username=response.data.datauser.username;
      
  } catch (error: any) {
      emit('close')
      const status = error.response?.status ?? 0;
      const data = error.response?.data ?? {'message':"Error inesperado"};
      console.log(error)
      if (status === 422) {
        if( data.errors){
          const formattedErrors: Record<string, string[]> = {};
          Object.entries(data.errors).forEach(([field, messages]) => {
            formattedErrors[field] = (messages as string[]); // tomamos el primer mensaje
          });
          ErrorUserForm.value=formattedErrors;
        } else{
          MyBasicToast.error(data.message ?? 'Errores de Validacion')
        }
        
      }else{
        MyBasicToast.error(data.message??'Ocurrio Un Error Inesperado')
      }
  }finally{
    cargando.value=undefined
  }
}
const buttonconfirm=computed<buttonconfirmed>(()=>{ 
  return {
    text:'Crear Empleado',
    classname:'bg-blue-600 text-white',
    onClick:Create,
    disabled:Object.entries(UserForm)
    .filter(([key]) => !['materno'].includes(key))
    .some(([_,value]) => value === null || value === '')}})


</script>

<template>

  <BaseModal 
    :modaltitle="(userid?'Editar':'Nuevo') +' Usuario Del Sitema'" 
    :position="'center'" 
    @close="emit('close')" 
    :show="props.show" 
    :buttonconfirm="buttonconfirm" >
    <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-2" v-if="cargando == undefined">
      <InputBasic id="name" label="Nombre" type="text" v-model="UserForm.name" :errors="ErrorUserForm['name'] ?? undefined"/>
      <InputBasic id="paterno" label="Paterno" type="text" v-model="UserForm.paterno" :errors="ErrorUserForm['paterno'] ?? undefined" />
      <InputBasic id="materno" label="Materno" type="text" v-model="UserForm.materno" :errors="ErrorUserForm['materno'] ?? undefined" />
      <InputBasic id="correo" label="Correo" type="email" v-model="UserForm.email" :errors="ErrorUserForm['email'] ?? undefined" />
      <InputBasic id="usuario" label="Usuario" type="text" v-model="UserForm.username" :errors="ErrorUserForm['username'] ?? undefined" />
      <InputBasic id="contraseña" label="Contraseña" type="password" v-model="UserForm.password" :errors="ErrorUserForm['password'] ?? undefined" />
      <InputBasic id="password_confirmation" label="Confirmar Contraseña" type="password" v-model="UserForm.password_confirmation" />
   </div>
   <Loading v-else :text="cargando"/>
  </BaseModal>
</template>
