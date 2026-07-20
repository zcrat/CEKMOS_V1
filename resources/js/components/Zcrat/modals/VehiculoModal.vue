<!-- ModalExample.vue -->
<script setup lang="ts">

import InputBasic from '../Inputs/form/InputBasic.vue'
import BaseModal from '@/components/Zcrat/modals/BaseModal.vue'
import { ref,reactive,computed,watch} from 'vue' 
import {type Vehiculo as VehiculoProps, type VehiculoForm, type option} from '@/types/generales'
import {type buttonconfirmed} from '@/types/modals'
import TiposVehiculos from '../Forms/TiposVehiculos.vue'
import axios from 'axios';
import MyBasicToast from '@/utils/ToastNotificationBasic'
import Loading from '../Elements/Loading.vue'
import ZDRemoteSelect from '@/components/Zcrat/Elements/ZDRemoteSelect.vue'

type CatalogType = 'marca' | 'motor' | 'modelo'
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
const hasMarca = computed(() => Vehiculo.marca !== null);
const hasModelo = computed(() => Vehiculo.modelo !== null);
const catalogType = ref<CatalogType>('marca')
const catalogShow = ref(false)
const catalogDescription = ref('')
const catalogErrors = ref<string[]>()
const catalogLoading = ref(false)
const catalogTitle = computed(() => catalogType.value === 'marca' ? 'Nueva marca' : `Nuevo ${catalogType.value}`)
const buttonconfirm=computed<buttonconfirmed>(()=>{ 
  return {
    text:'Guardar',
    classname:'bg-blue-600 text-white',
    onClick:Save,
    disabled:(Vehiculo.placas == '' || Vehiculo.economico == '' || Vehiculo.vin == '' || Vehiculo.año == ''|| Vehiculo.tipo_id == null || Vehiculo.color == ''|| Vehiculo.modelo == null || Vehiculo.marca == null || Vehiculo.motor == null )
}})

const catalogButtonConfirm=computed<buttonconfirmed>(()=>({
  text:'Guardar',
  classname:'bg-blue-600 text-white',
  onClick:SaveCatalog,
  disabled:catalogDescription.value.trim() === '' || catalogLoading.value,
}))


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

watch(() => Vehiculo.marca?.value, (marca, previousMarca) => {
  if (previousMarca !== undefined && marca !== previousMarca) {
    Vehiculo.modelo=null;
    Vehiculo.motor=null;
  }
})

watch(() => Vehiculo.modelo?.value, (modelo, previousModelo) => {
  if (previousModelo !== undefined && modelo !== previousModelo) {
    Vehiculo.motor=null;
  }
})

const OpenCatalog = (type:CatalogType) => {
  catalogType.value=type
  catalogDescription.value=''
  catalogErrors.value=undefined
  catalogShow.value=true
}

async function SaveCatalog() {
  if(catalogType.value === 'modelo') {
    const descripcion=catalogDescription.value.trim().toLocaleLowerCase('es-MX')
    Vehiculo.modelo={value:descripcion,label:descripcion}
    catalogShow.value=false
    return
  }

  try {
    catalogLoading.value=true
    catalogErrors.value=undefined
    const response=await axios.post(route('vehiculo.catalogo.create'),{
      tipo:catalogType.value,
      descripcion:catalogDescription.value,
    })
    const option:option=response.data.option
    if(catalogType.value === 'marca') Vehiculo.marca=option
    if(catalogType.value === 'motor') Vehiculo.motor=option
    catalogShow.value=false
  } catch (error:any) {
    catalogErrors.value=error.response?.data?.errors?.descripcion
      ?? [error.response?.data?.message ?? 'No fue posible guardar el catálogo']
  } finally {
    catalogLoading.value=false
  }
}

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
        <ZDRemoteSelect label="Marca" endpoint="select2.catalogo.marcas" v-model="Vehiculo.marca" :buttonNew="()=>OpenCatalog('marca')" :canNew="true" :cacheoptions="false" placeholder="Seleccionar marca" :errors="Vehiculo.error?.marca_id"/>
        <ZDRemoteSelect label="Modelo" endpoint="select2.catalogo.modelos" v-model="Vehiculo.modelo" :buttonNew="()=>OpenCatalog('modelo')" :canNew="true" :cacheoptions="false" :disabled="!hasMarca" :params="{ marca_id: Vehiculo.marca?.value }" placeholder="Seleccionar modelo" :errors="Vehiculo.error?.modelo"/>
        <ZDRemoteSelect label="Motor" endpoint="select2.catalogo.motores" v-model="Vehiculo.motor" :buttonNew="()=>OpenCatalog('motor')" :canNew="true" :cacheoptions="false" :disabled="!hasModelo" placeholder="Seleccionar motor" :errors="Vehiculo.error?.motor_id"/>
        <TiposVehiculos label="Tipo De Vehiculo" id="tipovehiculo" v-model="Vehiculo.tipo_id" />
      </div>
      <Loading  :text="loading" v-else/>
  </BaseModal>
  <BaseModal
    :show="catalogShow"
    :modaltitle="catalogTitle"
    position="center"
    z="z-[999]"
    :loading="catalogLoading"
    :buttonconfirm="catalogButtonConfirm"
    @close="catalogShow=false"
  >
    <div class="w-80 pb-2">
      <InputBasic
        id="CatalogDescription"
        label="Descripción"
        type="text"
        v-model="catalogDescription"
        placeholder="Descripción del nuevo registro"
        :errors="catalogErrors"
      />
    </div>
  </BaseModal>
</template>
