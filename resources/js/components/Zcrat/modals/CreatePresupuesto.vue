<!-- ModalExample.vue -->
<script setup lang="ts">

import InputBasic from '../Inputs/form/InputBasic.vue'
import Textarea from '../Inputs/form/Textarea.vue'
import BaseModal from '@/components/Zcrat/modals/BasicModal.vue'
import Create from '@/services/presupuesto/create'
import { ref, watch ,reactive, onMounted,computed} from 'vue' 
import Subtitle from '@/components/Zcrat/Elements/Subtitle.vue';
import {type NuevoPresupuesto, type option,type Vehiculo, type datagetpresupuestos} from '@/types/generales'
import {type buttonconfirmed} from '@/types/modals'
import Combobox from '@/components/Zcrat/Elements/ZdCombobox.vue'
import Datapicker from '@/components/Zcrat/Elements/ZDDataPicker.vue';
import Select from '@/components/Zcrat/Elements/Select.vue';
import Select2 from '@/components/Zcrat/Elements/ZDSelect.vue';
import debounce from 'lodash/debounce';
import GetNivelesGasolina from '@/utils/functions/select2/NivelesGasolina';
import GetModulosDisponibles  from '@/utils/functions/select2/ModulosCortana';
import { sumarDiasSinDomingo } from '@/utils/functions/generales/fechas';
import { useVehiculoFetcher } from '@/composables/useVehiculoFetchers';
import { usePresupuestoFetcher } from '@/composables/usePresupuestoFetcher'
const props = defineProps<{show: boolean}>()
const emit = defineEmits(['update:show'])
const optionstipos=ref<option[]>([{value:5,label:'Correctivo'},{value:6,label:'Preventivo'},{value:7,label:'Ambos'}])
const optionsgasolima=ref<option[]>([])
const empresa=ref<option|undefined>(undefined)
const cliente=ref<option|undefined>(undefined)
const vehiculoconcepto=ref<option|undefined>(undefined)
const modulosdisponibles=ref<option[]>([])

const updateVisibility = (val: boolean) => {
  emit('update:show', val)
}
onMounted(async () => {
 optionsgasolima.value = await GetNivelesGasolina();
 modulosdisponibles.value=await GetModulosDisponibles();
});
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
  garantia:'LO ESTIPULADO EN EL CONTRATO',
  observaciones: 'DE ACUERDO A LO DIFICIL DE LA FALLA PARA SU REPARACION',//tiempo de entrega
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
const cancelgetvehiculodata = ref(false);
const { fetchvehiculo } = useVehiculoFetcher(presupuesto,cancelgetvehiculodata);
const { fetchDatosPresupuesto } = usePresupuestoFetcher(presupuesto,empresa,cliente,vehiculoconcepto);
const buscarVehiculo = async (campo:string,value:string) => {
  await fetchvehiculo(campo, value);
};
const buscardatospresupuesto = async (value:string) => {
  await fetchDatosPresupuesto(value);
};
const debouncedGetDatosVehiculo = debounce(buscarVehiculo, 500);
watch(() => presupuesto.economico, (nuevoEconomico, anteriorEconomico) => {
  if (nuevoEconomico && nuevoEconomico !== anteriorEconomico && !cancelgetvehiculodata) {
    debouncedGetDatosVehiculo('economico', nuevoEconomico);
  }
});
watch(() => presupuesto.placas, (nuevasPlacas, anterioresPlacas) => {
  if (nuevasPlacas && nuevasPlacas !== anterioresPlacas && !cancelgetvehiculodata) {
    debouncedGetDatosVehiculo('placas', nuevasPlacas);
  }
});
watch(() => presupuesto.orden_servicio, (nuevaorden) => {
  buscardatospresupuesto(nuevaorden);
});

const buttonconfirm=computed<buttonconfirmed>(()=>{ 
  return {
    text:'Crear Presupuesto',
    classname:'bg-blue-600 text-white',
    onClick:()=>{
      Create(presupuesto).then((res)=>{
        if(res.status){
          updateVisibility(false);
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
    disabled:Object.entries(presupuesto)
    .filter(([key]) => !['orden_servicio','folio','orden_seguimiento','vigencia'].includes(key))
    .some(([_,value]) => value === null || value === '')}})

</script>

<template>
  <BaseModal modaltitle="Nuevo Presupuesto" :position="'center'" :modelValue="props.show" @update:modelValue="updateVisibility" :buttonconfirm="buttonconfirm" >
    <Subtitle>Datos Generales</Subtitle>
    <div class="grid sm:grid-cols-2 md:grid-cols-3 gap-2" >
      <Combobox endpoint="Combobox.Ordenes_Servicio" label="Orden De Servicio" id="ordenservicio" v-model="presupuesto.orden_servicio"  placeholder="Buscar, Crear o Automatica"/>
      <InputBasic id="folio" label="folio" type="text" v-model="presupuesto.folio" placeholder="Automatico O Ingresar"/>
      <InputBasic id="ordenseguimiento" label="Orden De Seguimiento" type="text" v-model="presupuesto.orden_seguimiento" placeholder="Automatico O Ingresar"/>
      <Combobox id="ubicacion" label="Ubicacion" v-model="presupuesto.ubicacion" endpoint="Combobox.ubicaciones" placeholder="Buscar o Crear"/>
      <Datapicker label="Fecha Estimada" v-model="presupuesto.estimacion" :clearable="false" :time="true" :range="false" class="w-full"/>
      <InputBasic id="kilometraje" label="Kilometraje" type="number" v-model="presupuesto.kilometraje" placeholder="ej. 392.31"/>
      <Select id="gasolina" :canempty="true" v-model="presupuesto.gasolina" label="Gasolina" :options="optionsgasolima"></Select>
      <Select label="Tipo De Presupuesto"  v-model="presupuesto.tipo_id" id="presupuestotipo"  :options="optionstipos"></Select>
      <Select2 :new_option="vehiculoconcepto" label="Vehiculo De Los Conceptos" endpoint="Select2.Empresas" v-model="presupuesto.vehiculo_concepto_id" id="presupuestovehiculoconcepto" placeholder="Buscar Cliente" />
      <Select2 :new_option="empresa" label="Empresa" id="presupuestoempresa" endpoint="Select2.Empresas" v-model="presupuesto.empresa_id" placeholder="Buscar Empresas"/>
      <Select2 :new_option="cliente" label="Cliente" endpoint="Select2.Empresas" v-model="presupuesto.cliente_id" id="presupuestoempresa" placeholder="Buscar Cliente"/>
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
