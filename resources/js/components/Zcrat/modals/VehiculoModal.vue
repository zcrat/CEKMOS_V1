<!-- ModalExample.vue -->
<script setup lang="ts">

import BaseModal from '@/components/Zcrat/modals/BaseModal.vue'
import { ref,reactive,computed,watch} from 'vue' 
import {type Vehiculo as VehiculoProps, type VehiculoForm} from '@/types/generales'
import { type ConfirmButton } from '@/types/modals'
import axios from 'axios';
import MyBasicToast from '@/utils/ToastNotificationBasic'
import Loading from '../Elements/Loading.vue'
import VehiculoFields from '@/components/Zcrat/Forms/VehiculoFields.vue'

const props = defineProps<{
  show: boolean
  id?:number|string
  returnSave?:(val:VehiculoProps)=>void
}>()
const emit = defineEmits<{
  (e: 'close'): void
}>()
const Vehiculo = reactive<VehiculoForm>({
  placas:'',
  economico:'',
  vin:'',
  año:'',
  tipo_id:null,
  color:'',
  modelo:null,
  marca:null,
  motor:null,
});
const loading = ref<string|null>(null);
const confirmButton=computed<ConfirmButton>(()=>{
  return {
    text:'Guardar',
    className:'bg-blue-600 text-white',
    onClick:Save,
    disabled:(Vehiculo.placas == '' || Vehiculo.economico == '' || Vehiculo.vin == '' || Vehiculo.año == ''|| Vehiculo.tipo_id == null || Vehiculo.color == ''|| Vehiculo.modelo == null || Vehiculo.marca == null || Vehiculo.motor == null )
}})

watch(()=>props,()=>{
  if(props.id && props.show){
    Read();
  }else{
    Vehiculo.placas='';
    Vehiculo.economico='';
    Vehiculo.vin='';
    Vehiculo.año='';
    Vehiculo.tipo_id=null;
    Vehiculo.color='';
    Vehiculo.modelo=null;
    Vehiculo.marca=null;
    Vehiculo.motor=null;
    Vehiculo.error=undefined;
  }
},{deep:true})

const Save = async () => {
  try {
    loading.value='Guardando Vehiculo';
    Vehiculo.error=undefined;
    const response = await axios.post(route('vehiculo.createorupdate'),{
      id:props.id,
      placas:Vehiculo.placas,
      economico:Vehiculo.economico,
      vin:Vehiculo.vin,
      año:Vehiculo.año,
      tipo_id:Vehiculo.tipo_id,
      color:Vehiculo.color,
      marca_id:Vehiculo.marca?.value,
      motor_id:Vehiculo.motor?.value,
      modelo:Vehiculo.modelo?.label,
    })
    props.returnSave?.(response.data.vehiculo)
    MyBasicToast.success(response.data.message??'Guardo Correctamente')
    emit('close')
  } catch (error: any) {
    const status = error.response?.status ?? 0;
    const data = error.response?.data;
    if (status === 422 && data.errors) {
      Vehiculo.error=data.errors
    }else{
      MyBasicToast.success(data.message??'Error Indefinido')
    }
  }finally{
    loading.value=null;
  }
}
const Read = async () => {
  try {
    loading.value='Cargando Datos';
    const response = await axios.get(route('vehiculo.find'),{params:{id:props.id} })
    const data:VehiculoProps=response.data.vehiculo;
    Vehiculo.placas=data.placas;
    Vehiculo.economico=data.economico;
    Vehiculo.vin=data.vin;
    Vehiculo.año=''+data.año;
    Vehiculo.tipo_id=Number(data.tipo_id);
    Vehiculo.color=data.color?.descripcion ?? 'No Encontrado';
    Vehiculo.marca=data.modelo?.marca ? {value:data.modelo.marca.id!,label:data.modelo.marca.descripcion} : null;
    Vehiculo.modelo=data.modelo ? {value:data.modelo.descripcion,label:data.modelo.descripcion} : null;
    Vehiculo.motor=data.modelo?.motor ? {value:data.modelo.motor.id!,label:data.modelo.motor.descripcion} : null;
  } catch (error: any) {
    console.error('Error:', error)
    emit('close')
  }finally{
    loading.value=null;
  }
}
</script>

<template>
 <BaseModal modal-title="Nueva Economico"
  :position="'center'" 
  :show="props.show" 
  @close="emit('close')" 
  :confirm-button="confirmButton"
  :loading="loading != null"
  >
    <VehiculoFields
      v-if="loading == null"
      v-model:economico="Vehiculo.economico"
      v-model:placas="Vehiculo.placas"
      v-model:vin="Vehiculo.vin"
      v-model:anio="Vehiculo.año"
      v-model:color="Vehiculo.color"
      v-model:tipoId="Vehiculo.tipo_id"
      v-model:marca="Vehiculo.marca"
      v-model:modelo="Vehiculo.modelo"
      v-model:motor="Vehiculo.motor"
      :errors="Vehiculo.error"
    />
      <Loading  :text="loading" v-else/>
  </BaseModal>
</template>
