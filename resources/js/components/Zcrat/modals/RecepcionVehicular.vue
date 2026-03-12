<!-- ModalExample.vue -->
<script setup lang="ts">
  import InputBasic from '../Inputs/form/InputBasic.vue'
  import Textarea from '../Inputs/form/Textarea.vue'
  import BaseModal from '@/components/Zcrat/modals/BasicModal.vue'
  import Create from '@/services/presupuesto/create'
  import { ref, watch ,reactive, onMounted,computed} from 'vue' 
  import Subtitle from '@/components/Zcrat/Elements/Subtitle.vue';
  import {type option,type Vehiculo, type datagetpresupuestos} from '@/types/generales'
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
  import ZDCanvas from '../Elements/ZDCanvas.vue'
  import axios from 'axios' 
  import MyBasicToast from "@/utils/ToastNotificationBasic";
  const props = defineProps<{show: boolean}>()
  const emit = defineEmits(['close'])
  const optionstipos=ref<option[]>([{value:5,label:'Correctivo'},{value:6,label:'Preventivo'},{value:7,label:'Ambos'}])
  const optionsgasolima=ref<option[]>([])
  const empresa=ref<option|undefined>(undefined)
  const cliente=ref<option|undefined>(undefined)
  const vehiculoconcepto=ref<option|undefined>(undefined)
  const modulosdisponibles=ref<option[]>([])

  const updateVisibility = () => {
    emit('close')
  }
  onMounted(async () => {
  optionsgasolima.value = await GetNivelesGasolina();
  modulosdisponibles.value=await GetModulosDisponibles();
  });

  export interface RecepcionVehicularForm {
    id?:number
    orden_seguimiento: string,
    orden_opcional: string,
    folio: string,
    ubicacion: string,
    telefono: number | null,
    empresa_id: number|null,
    cliente_id:number|null,
    gasolina: number | string,
    kilometraje: number | null,
    estimacion: Date,
    administrador: string,
    jefe: string,
    trabajador: string,
    tecnico: string,
    descripcion_mo: string,
    indicaciones_cliente: string,
    garantia: string,
    observaciones: string,//tiempo de entrega
    tipo_id: 5|6|7
    vehiculo_concepto_id: number| null,
    economico: string,
    placas: string,
    vin: string,
    marca: string,
    modelo: string,
    año: number|null,
    modulo_orden: number|string,
    //datos posibles
    vigencia: Date|null,
  }

  const presupuesto = reactive<RecepcionVehicularForm>({
    orden_seguimiento: '',
    orden_opcional: '',
    folio: '',
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
    tipo_id:7,//correctivo, preventivo,ambos
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

  const KeysOptional=['orden_opcional','folio','orden_seguimiento','vigencia'];

  const ImageVehiculoEntrada = ref<InstanceType<typeof ZDCanvas> | null>(null);



  const buttonconfirm=computed<buttonconfirmed>(()=>{ 
    return {
      text:'Crear Presupuesto',
      classname:'bg-blue-600 text-white',
      onClick:()=>{
        
      },
      disabled:Object.entries(presupuesto)
      .filter(([key]) => !KeysOptional.includes(key))
      .some(([_,value]) => value === null || value === '')}})

  const prueba = async () => {
    try {
      const response = await axios.get(route('image.tipo.vehiculo', { type: 1 }), {
        responseType: 'blob'
      });
      ImageVehiculoEntrada.value?.dibujarImagen(response.data);
    } catch (error:any) {
      if (error.response) {
        const blob = error.response.data;
        const contentType = error.response.headers['content-type'];
        if (contentType && contentType.includes('application/json')) {
          const text = await blob.text();
          const jsonError = JSON.parse(text);
          MyBasicToast.error(jsonError.message || 'Error al encontrar la imagen de referencia');
        } else {
          MyBasicToast.error('Error desconocido del servidor');
        }
      } else {
        MyBasicToast.error('Error al conectar con el servidor');
      }
    }
  }
</script>

<template>
  <BaseModal modaltitle="Nuevo Presupuesto" :position="'center'" :show="props.show" @close="updateVisibility" :buttonconfirm="buttonconfirm" >
    <Subtitle>Datos Generales</Subtitle>
    <div class="grid sm:grid-cols-2 md:grid-cols-3 gap-2" >
      <InputBasic id="ordenseguimiento" label="Orden De Seguimiento" type="text" v-model="presupuesto.orden_seguimiento" placeholder="Automatico o Ingresar"/>
      <InputBasic id="ordenseguimiento" label="Orden Opcional" type="text" v-model="presupuesto.orden_opcional" placeholder="Opcional"/>
      <Combobox id="ubicacion" label="Ubicacion" v-model="presupuesto.ubicacion" endpoint="Combobox.ubicaciones" placeholder="Buscar o Crear"/>

      <Select label="Tipo De Presupuesto"  v-model="presupuesto.tipo_id" id="presupuestotipo"  :options="optionstipos"></Select>
      <Select label="Modulo Orden"  v-model="presupuesto.modulo_orden" id="modulooreden" :canempty="true" :options="modulosdisponibles"></Select>
      <Select2 :new_option="vehiculoconcepto" label="Vehiculo De Los Conceptos" endpoint="Select2.Empresas" v-model="presupuesto.vehiculo_concepto_id" id="presupuestovehiculoconcepto" placeholder="Buscar Cliente" />
      
    </div>
    <Subtitle>Datos Cliente</Subtitle>
    <div class="grid sm:grid-cols-2 gap-2">
      <Select2 :new_option="empresa" label="Empresa" id="presupuestoempresa" endpoint="Select2.Empresas" v-model="presupuesto.empresa_id" placeholder="Buscar Empresas"/>
      <Select2 :new_option="cliente" label="Cliente" endpoint="Select2.Empresas" v-model="presupuesto.cliente_id" id="presupuestoempresa" placeholder="Buscar Cliente"/>
    </div>
    <Subtitle>Datos Vehiculo</Subtitle>
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-2 mt-2">
      <Combobox endpoint="Combobox.Vehiculo.Economico" label="Economico" id="economico" v-model="presupuesto.economico"  placeholder="Buscar o Crear Economico" :timeout="1"/>
      <Combobox endpoint="Combobox.Vehiculo.Placas" label="Placas" id="placas" v-model="presupuesto.placas"  placeholder="Buscar o Crear PLacas"/>
      <InputBasic id="Vin" label="Vin" type="text" v-model="presupuesto.vin" placeholder="Ej.JJSOE18P388988750 "/>
      <InputBasic id="Año" label="Año" type="number" v-model="presupuesto.año"  placeholder="ej. 2024"/>
      <InputBasic id="Marca" label="Marcas" type="text" v-model="presupuesto.marca" classname="uppercase" placeholder="ej. AUDI"/>
      <InputBasic id="Modelo" label="Modelo" type="text" v-model="presupuesto.modelo" classname="uppercase" placeholder="ej. A3"/>
      <ZDCanvas class="col-span-3" ref="ImageVehiculoEntrada"></ZDCanvas>
      <button @click="prueba()">prueba</button>
    </div>
    <Subtitle>Datos De Ingreso</Subtitle>
    <div class="grid sm:grid-cols-2 md:grid-cols-3 gap-2" >
      <Datapicker label="Fecha Estimada" v-model="presupuesto.estimacion" :clearable="false" :time="true" :range="false" class="w-full sm:col-span-2 md:col-span-1"/>
      <InputBasic id="kilometraje" label="Kilometraje" type="number" v-model="presupuesto.kilometraje" placeholder="ej. 392.31"/>
      <Select id="gasolina" :canempty="true" v-model="presupuesto.gasolina" label="Gasolina" :options="optionsgasolima"></Select>
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
    
  </BaseModal>
</template>
