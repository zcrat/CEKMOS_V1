<!-- ModalExample.vue -->
<script setup lang="ts">

import InputBasic from '../Inputs/form/InputBasic.vue'
import Textarea from '../Inputs/form/Textarea.vue'
import BaseModal from '@/components/Zcrat/modals/BaseModal.vue'
import Create from '@/services/employee/create'
import { ref,reactive,computed,watch} from 'vue' 
import Subtitle from '@/components/Zcrat/Elements/Subtitle.vue';
import {type Vehiculo as VehiculoProps, type VehiculoForm, type option} from '@/types/generales'
import {type buttonconfirmed} from '@/types/modals'
import Select2 from '@/components/Zcrat/Elements/ZDSelect.vue';
import TiposVehiculos from '../Forms/TiposVehiculos.vue'
import axios from 'axios';
import MyBasicToast from '@/utils/ToastNotificationBasic'
import Loading from '../Elements/Loading.vue'
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
  modelo:'',
  marca:'',
});
const loading = ref<string|null>(null);
const buttonconfirm=computed<buttonconfirmed>(()=>{ 
  return {
    text:'Guardar',
    classname:'bg-blue-600 text-white',
    onClick:Save,
    disabled:(Vehiculo.placas == '' || Vehiculo.economico == '' || Vehiculo.vin == '' || Vehiculo.año == ''|| Vehiculo.tipo_id == null || Vehiculo.color == ''|| Vehiculo.modelo == ''|| Vehiculo.marca == '' )
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
    Vehiculo.modelo='';
    Vehiculo.marca='';
    Vehiculo.error=undefined;
  }
},{deep:true})

const Save = async () => {
  try {
    loading.value='Guardando Vehiculo';
    Vehiculo.error=undefined;
    const response = await axios.post(route('Vehiculo.CreateOrUpdate'),{'id':props.id,...Vehiculo})
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
    const response = await axios.get(route('Vehiculo.Find'),{params:{id:props.id} })
    const data:VehiculoProps=response.data.vehiculo;
    Vehiculo.placas=data.placas;
    Vehiculo.economico=data.economico;
    Vehiculo.vin=data.vin;
    Vehiculo.año=''+data.año;
    Vehiculo.tipo_id=Number(data.tipo_id);
    Vehiculo.color=data.color?.descripcion ?? 'No Encontrado';
    Vehiculo.modelo=data.modelo?.descripcion ?? 'No Encontrado';
    Vehiculo.marca=data.modelo?.marca?.descripcion ?? 'No Encontrado';
  } catch (error: any) {
    console.error('Error:', error)
    emit('close')
  }finally{
    loading.value=null;
  }
}
</script>

<template>
 <BaseModal modaltitle="Nueva Economico" 
  :position="'center'" 
  :show="props.show" 
  @close="emit('close')" 
  :buttonconfirm="buttonconfirm" 
  :loading="loading != null"
  >
    <div class="grid sm:grid-cols-2  lg:grid-cols-4 gap-2" v-if="loading == null">
        <InputBasic id="Economico" label="Economico" type="text" placeholder="ej. 254335" v-model="Vehiculo.economico" :errors="Vehiculo.error?.economico"/>
        <InputBasic id="Placas" label="Placas" type="text" placeholder="ej. PHU234T" v-model="Vehiculo.placas" :errors="Vehiculo.error?.placas"/>
        <InputBasic id="Vin" label="Vin" type="text" v-model="Vehiculo.vin" placeholder="Ej.JJSOE18P388988750 " :errors="Vehiculo.error?.vin"/>
        <InputBasic id="Año" label="Año" type="number" v-model="Vehiculo.año"  placeholder="ej. 2024" :errors="Vehiculo.error?.año"/>
        <InputBasic id="Color" label="Color" type="text" v-model="Vehiculo.color" classname="uppercase" placeholder="ej. ROJO" :errors="Vehiculo.error?.color"/>
        <InputBasic id="Marca" label="Marca" type="text" v-model="Vehiculo.marca" classname="uppercase" placeholder="ej. AUDI" :errors="Vehiculo.error?.marca"/>
        <InputBasic id="Modelo" label="Modelo" type="text" v-model="Vehiculo.modelo" classname="uppercase" placeholder="ej. A3" :errors="Vehiculo.error?.modelo"/>
        <TiposVehiculos label="Tipo De Vehiculo" id="tipovehiculo" v-model="Vehiculo.tipo_id" />
      </div>
      <Loading  :text="loading" v-else/>
  </BaseModal>
</template>
