<!-- ModalExample.vue -->
<script setup lang="ts">

import InputBasic from '../Inputs/form/InputBasic.vue'
import Textarea from '../Inputs/form/Textarea.vue'
import BaseModal from '@/components/Zcrat/modals/BasicModal.vue'
import ButtonTogglePYR from '@/components/Zcrat/Inputs/ButtonTogglePYR.vue'
import MyBasicToast from '@/utils/ToastNotificationBasic'
import axios from 'axios'
import { ref, watch ,reactive, onMounted, watchEffect} from 'vue' 
import Subtitle from '@/components/Zcrat/Elements/Subtitle.vue';
import {type NuevoPresupuesto, type option,type Vehiculo } from '@/utils/interfaces/generales'
import Combobox from '@/components/Zcrat/Elements/ZdCombobox.vue'
import Datapicker from '@/components/Zcrat/Elements/ZDDataPicker.vue';
import Select from '@/components/Zcrat/Elements/Select.vue';
import Select2 from '@/components/Zcrat/Elements/ZDSelect.vue';
import debounce from 'lodash/debounce';
const props = defineProps<{show: boolean}>()
const emit = defineEmits(['update:show'])
const optionstipos=ref<option[]>([{value:1,label:'Correctivo'},{value:2,label:'Preventivo'},{value:3,label:'Ambos'}])
const optionsgasolima=ref<option[]>([])
const modulosdisponibles=ref<option[]>([])
const updateVisibility = (val: boolean) => {
  emit('update:show', val)
}

onMounted(() => {
  GetNivelesGasolina();
  GetModulosDisponibles();
});

const GetNivelesGasolina = async () => {
  try {
    const response = await axios.get(route('select.niveles.combustible'))
    optionsgasolima.value = response.data.options
  } catch (error: any) {
    console.error(error)
  } 
}
const GetModulosDisponibles = async () => {
  try {
    const response = await axios.get(route('select.modulos.disponibles.usuario'))
    modulosdisponibles.value = response.data.options
  } catch (error: any) {
    console.error(error)
  } 
}



const sumarDiasSinDomingo=(fecha: Date, dias: number): Date=> {
  const resultado = new Date(fecha);
  let diasSumados = 0;

  while (diasSumados < dias) {
    resultado.setDate(resultado.getDate() + 1);
    if (resultado.getDay() !== 0) {
      diasSumados++;
    }
  }

  return resultado;
}

const presupuesto = reactive<NuevoPresupuesto>({
  orden_servicio: '',
  folio: '',
  orden_seguimiento: '',
  ubicacion: '',
  telefono: null,
  empresa_id: null,
  cliente_id:null,
  gasolina: '',
  kilometraje: null,
  estimacion: sumarDiasSinDomingo(new Date(new Date().setHours(12,0)),2),
  administrador: '',
  jefe: '',
  trabajador: '',
  tecnico: '',
  descripcion_mo: '',
  indicaciones_cliente: '',
  garantia:'',
  observaciones: '',//tiempo de entrega
  tipo_id:3,//correctivo, preventivo,ambos
  vehiculo_concepto_id: null,
  economico: '',
  placas: '',
  vin: '',
  marca: '',
  modelo: '',
  año: null,
  vigencia: null,
  modulo_orden:''
});

const GetDatosVehiculo = async (filter:string,value:string) => {
  try {
    const Vehiculo:Vehiculo|null = await axios.get(route('Vehiculo.Get.Datos'),{params:{filter,value}})
    if(Vehiculo){
      presupuesto.economico=Vehiculo.economico;
      presupuesto.placas=Vehiculo.placas;
      presupuesto.vin=Vehiculo.vin;
      presupuesto.marca=Vehiculo.marca??'';
      presupuesto.modelo=Vehiculo.modelo??'';
      presupuesto.año=Vehiculo.año;
    }
  } catch (error: any) {
    console.error(error)
  } 
}
const debouncedGetDatosVehiculo = debounce(GetDatosVehiculo, 500);

watch(() => presupuesto.economico, (nuevoEconomico, anteriorEconomico) => {
  console.log('Economico changed:', nuevoEconomico);
  if (nuevoEconomico && nuevoEconomico !== anteriorEconomico) {
    debouncedGetDatosVehiculo('economico', nuevoEconomico);
  }
});

watch(() => presupuesto.placas, (nuevasPlacas, anterioresPlacas) => {
  if (nuevasPlacas && nuevasPlacas !== anterioresPlacas) {
    debouncedGetDatosVehiculo('placas', nuevasPlacas);
  }
});
</script>

<template>
  <BaseModal modaltitle="Nuevo Presupuesto" :position="'center'" :modelValue="props.show" @update:modelValue="updateVisibility" >
    <Subtitle>Datos Generales</Subtitle>
    <div class="grid sm:grid-cols-2 md:grid-cols-3 gap-2" >
      <Combobox endpoint="Combobox.Ordenes_Servicio" label="Orden De Servicio" id="ordenservicio" v-model="presupuesto.orden_servicio"  placeholder="Buscar, Crear o Automatica"/>
      <InputBasic id="folio" label="folio" type="text" v-model="presupuesto.folio" placeholder="Automatico O Ingresar"/>
      <InputBasic id="ordenseguimiento" label="Orden De Seguimiento" type="text" v-model="presupuesto.orden_seguimiento" placeholder="Automatico O Ingresar"/>
      <Combobox id="ubicacion" label="Ubicacion" v-model="presupuesto.ubicacion" endpoint="Combobox.ubicaciones" placeholder="Buscar o Crear"/>
      <Datapicker label="Fecha Estimada" v-model="presupuesto.estimacion" :clearable="false" :time="true" :range="false" class="w-full"/>
      <InputBasic id="kilometraje" label="Kilometraje" type="number" v-model="presupuesto.kilometraje" placeholder="ej. 392.31"/>
      <Select id="gasolina" :canempty="true" v-model="presupuesto.gasolina" label="Gasolina" :options="optionsgasolima"></Select>
      <Select label="Tipo De Presupuesto"  v-model="presupuesto.tipo_id" id="presupuestocliente"  :options="optionstipos"></Select>
      <Select2 label="Vehiculo De Los Conceptos" endpoint="Select2.Empresas" v-model="presupuesto.vehiculo_concepto_id" id="presupuestocliente" placeholder="Buscar Cliente" />
      <Select2 label="Empresa" endpoint="Select2.Empresas" v-model="presupuesto.empresa_id" id="presupuestoempresa" placeholder="Buscar Empresas"/>
      <Select2 label="Cliente" endpoint="Select2.Empresas" v-model="presupuesto.cliente_id" id="presupuestocliente" placeholder="Buscar Cliente"/>
      <Select label="Modulo Orden"  v-model="presupuesto.modulo_orden" id="modulooreden" :canempty="true" :options="modulosdisponibles"></Select>
    </div>
    <Subtitle>Empleados Encargados</Subtitle>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-2" >
      <Combobox endpoint="Combobox.Administradores_Trasporte" label="Administrador de Trasportes" id="administradortrasporte" v-model="presupuesto.administrador"  placeholder="Buscar o Crear "/>
      <Combobox endpoint="Combobox.Jefes_Procesos" label="Jefe De Procesos" id="jefeproceso" v-model="presupuesto.jefe"  placeholder="Buscar o Crear"/>
      <Combobox endpoint="Combobox.Trabajadores" label="Trabajador" id="trabajador" v-model="presupuesto.trabajador"  placeholder="Buscar o Crear"/>
      <Combobox endpoint="Combobox.Tecnicos" label="Tecnico" id="tecnico" v-model="presupuesto.tecnico"  placeholder="Buscar o Crear"/>
    </div>
    <Subtitle>Notas</Subtitle>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-2" >
      <Textarea id="indicacionescliente" label="Indicaciones Del Cliente" v-model="presupuesto.indicaciones_cliente" placeholder="Escribe las indicaciones del cliente aqui..." classname="h-24"/>
      <Textarea id="notasmecanico" label="Descripcion Mano De Obra" v-model="presupuesto.descripcion_mo" placeholder="Escribe las notas del mecanico aqui..." classname="h-24"/>
      <Textarea id="observaciones" label="Garantia" v-model="presupuesto.garantia" placeholder="Escribe las observaciones aqui..." classname="h-24"/>
      <Textarea id="descripcionmo" label="Tiempo de Entrega" v-model="presupuesto.observaciones" placeholder="Escribe la descripcion de la mano de obra aqui..." classname="h-24"/>
    </div>
    <Subtitle>Datos Vehiculo</Subtitle>
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-2 mt-2">
      <Combobox endpoint="Combobox.Vehiculo.Economico" label="Economico" id="economico" v-model="presupuesto.economico"  placeholder="Buscar o Crear Economico" :timeout="1"/>
      <Combobox endpoint="Combobox.Vehiculo.Placas" label="Placas" id="placas" v-model="presupuesto.placas"  placeholder="Buscar o Crear PLacas"/>
      <InputBasic id="Vin" label="Vin" type="text" v-model="presupuesto.vin" placeholder="Ej.JJSOE18P388988750 "/>
      <InputBasic id="Año" label="Año" type="number" v-model="presupuesto.año"  placeholder="ej. 2024"/>
      <InputBasic id="Marca" label="Marcas" type="text" v-model="presupuesto.marca" classname="uppercase" placeholder="ej. AUDI"/>
      <InputBasic id="Modelo" label="Modelo" type="text" v-model="presupuesto.modelo" classname="uppercase" placeholder="ej. A3"/>
    </div>
  </BaseModal>
</template>
