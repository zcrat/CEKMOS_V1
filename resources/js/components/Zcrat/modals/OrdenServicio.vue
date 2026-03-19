<!-- ModalExample.vue -->
<script setup lang="ts">
  import InputBasic from '../Inputs/form/InputBasic.vue'
  import Textarea from '../Inputs/form/Textarea.vue'
  import BaseModal from '@/components/Zcrat/modals/BasicModal.vue'
  import { ref, watch ,reactive, onMounted,computed} from 'vue' 
  import Subtitle from '@/components/Zcrat/Elements/Subtitle.vue';
  import {type option,type Vehiculo, type datagetpresupuestos} from '@/types/generales'
  import {type buttonconfirmed} from '@/types/modals'
  import Combobox from '@/components/Zcrat/Elements/ZdCombobox.vue'
  import Datapicker from '@/components/Zcrat/Elements/ZDDataPicker.vue';
  import Select from '@/components/Zcrat/Elements/Select.vue';
  import Select2 from '@/components/Zcrat/Elements/ZDSelect.vue';
  import debounce from 'lodash/debounce';
  import GetNivelesGasolina from '@/utils/functions/select/NivelesGasolina';
  import GetModulosDisponibles  from '@/utils/functions/select/ModulosCortana';
  import { sumarDiasSinDomingo } from '@/utils/functions/generales/fechas';
  import { useVehiculoFetcher } from '@/composables/useVehiculoFetchers';
  import { usePresupuestoFetcher } from '@/composables/usePresupuestoFetcher'
  import ZDCanvas from '../Elements/ZDCanvas.vue'
  import axios from 'axios' 
  import MyBasicToast from "@/utils/ToastNotificationBasic";
  import TiposVehiculos from '../Forms/TiposVehiculos.vue';
  import GetStatusPerCategory from '@/utils/functions/select/StatusPerCategory'
import Checkbox from '../Inputs/form/Checkbox.vue';

  export interface Economico {
    id?:number,
    economico: string,
    placas: string,
    vin: string,
    anio:number|"",
    marca:string,
    modelo:string,
    tipo_id: number|""
  }
  export interface OrdenServicioForm {
    id?:number
    orden_seguimiento: string,
    orden_opcional: string,
    ubicacion: string,
    tipo_id: 5|6|7
    modulo_orden: number|string,
    vehiculo_concepto_id: number| null,
    empresa_id: number|null,
    cliente_id:number|null,
    telefono: number | null,
    estimacion: Date,
    kilometraje: number | null,
    gasolina: number | string,
    administrador: string,
    jefe: string,
    trabajador: string,
    tecnico: string,
    indicaciones_cliente: string,
    descripcion_mo: string,
    garantia: string,
    observaciones: string,
  }
  export interface ImagenesForm {
    ids?:{
      id:number
      DetallesGeneralesid:number
    },
    image:Blob,
    tipo_id:number
  }
  export interface PinturaForm{
    id?:number,
    DetallesGeneralesId?:number,
    decolarada:"1"|"",
    color_desigual:"1"|"",
    rayonnes:"1"|"",
    grietas:"1"|"",
    golpes:"1"|"",
    emblemas:"1"|"",
    logos:"1"|"",
    rociado:"1"|"",
    granizo:"1"|"",
    lluvia:"1"|"",
  }
  export interface InventarioForm{
    id?:number,
    DetallesGeneralesId?:number,
    llanta:"1"|"",
    cables:"1"|"",
    estuche:"1"|"",
    llave_tuerca:"1"|"",
    triangulo:"1"|"",
    tarjeta_circulacion:"1"|"",
    cubreruedas:"1"|"",
    candado_rueda:"1"|"",
    extinguidor:"1"|"",
    gato:"1"|"",
    placas:"1"|"",
  }
  export interface CondicionesInterioresForm{
    id?:number,
    DetallesGeneralesId?:number,
    puerta_izq_f:string,
    puerta_izq_t:string,
    puerta_der_f:string,
    puerta_der_t:string,
    asiento_izq_f:string,
    asiento_izq_t:string,
    asiento_der_f:string,
    asiento_der_t:string,
    consola:string,
    claxon:string,
    tablero:string,
    quemacocos:string,
    toldo:string,
    elevadores:string,
    luces:string,
    seguros:string,
    climatizador:string,
    radio:string,
    retrovisor:string,
    tapetes:string,
  }
  export interface CondicionesExterioresForm{
    id?:number,
    DetallesGeneralesId?:number,
    antena_radio:string,
    estribos:string,
    antena_telefono:string,
    guardafangos:string,
    antena_cb:string,
    parabrisas:string,
    alarma:string,
    limpiaparabrisas:string,
    luces:string,
    espejos_laterales:string
  }

  const emit = defineEmits(['close'])
  const props = defineProps<{show: boolean}>()
  const optionstipos=[
    {value:5,label:'Correctivo'},
    {value:6,label:'Preventivo'},
    {value:7,label:'Ambos'}
  ];
  const CondicionesExterioresInputs={
    'antena_radio':'ANTENA/RADIO',
    'estribos':'ESTRIBOS',
    'antena_telefono':'ANTENA/TELEFONO',
    'guardafangos':'GUARDAFANGOS',
    'antena_cb':'ANTENA/C.B',
    'parabrisas':'PARABRISAS',
    'alarma':'SISTEMA DE ALARMA',
    'limpiaparabrisas':'LIMPIAPARABRISAS',
    'luces':'LUCES EXTERIORES',
    'espejos_laterales':'ESPEJOS LATERALES'
  }
  const CondicionesInterioresInputs={
    'PANALES DE PUERTA':{
      'puerta_izq_f':'IZQUIERDA FRONTAL',
      'puerta_izq_t':'IZQUIERDA TRASERA',
      'puerta_der_f':'DERECHA FRONTAL',
      'puerta_der_t':'DERECHA TRASERA',
    },
    'ASIENTOS':{
      'asiento_izq_f':'IZQUIERDO FRONTAL',
      'asiento_izq_t':'IZQUIERDO TRASERO',
      'asiento_der_f':'DERECHO FRONTAL',
      'asiento_der_t':'DERECHO TRASERO',
    },
    'OTROS':{
      'consola':'CONSOLA CENTRAL',
      'claxon':'CLAXON',
      'tablero':'TABLERO',
      'quemacocos':'QUEMACOCOS',
      'toldo':'TOLDO',
      'elevadores':'ELEVADORES ELEC.',
      'luces':'LUCES INTERIORES',
      'seguros':'SEGUROS ELEC.',
      'climatizador':'CLIMATIZADOR',
      'radio':'RADIO',
      'retrovisor':'ESPEJO RETROVISOR',
      'tapetes':'TAPETES'
    }
  }
  const InventarioInputs={
    'llanta':'LLANTA REFACCION',
    'cables':'CABLES PARA CORRIENTE',
    'estuche':'ESTUCHE DE HERRAMIENTAS',
    'llave_tuerca':'LLAVE TUERCAS DE RUEDA',
    'triangulo':'TRIANGULO DE SEGURO',
    'tarjeta_circulacion':'TARJETA DE CIRCULACION',
    'cubreruedas':'CUBRERUEDAS',
    'candado_rueda':'CANDADO DE RUEDA',
    'extinguidor':'EXTINGUIDOR',
    'gato':'GATO',
    'placas':'PLACAS'
  }
  const PinturaInputs={
    'decolarada':'DECOLORADA',
    'color_desigual':'COLOR NO IGULADO',
    'rayonnes':'EXCESO DE RAYONES',
    'grietas':'PEQUEÑAS GRIETAS',
    'golpes':'CARROCERIA CON GOLPES',
    'emblemas':'EMBLEMAS COMPLETOS',
    'logos':'LOGOS EN BUEN ESTADO',
    'rociado':'EXCESO DE ROCIADO',
    'granizo':'DAÑOS POR GRANIZO',
    'lluvia':'LLUVIA ACIDA'
  }
  const optionsgasolima=ref<option[]>([])
  const optionsequipo=ref<option[]>([])
  const modulosdisponibles=ref<option[]>([])
  
  const empresa=ref<option|undefined>(undefined)
  const cliente=ref<option|undefined>(undefined)
  const vehiculoconcepto=ref<option|undefined>(undefined)

  const updateVisibility = () => {
    emit('close')
  }
  onMounted(async () => {
  optionsgasolima.value = await GetNivelesGasolina();
  optionsequipo.value = await GetStatusPerCategory(11);
  modulosdisponibles.value=await GetModulosDisponibles();
  });

  const Economico = reactive<Economico>({
    id:undefined,
    economico: "",
    placas: "",
    vin: "",
    anio:"",
    marca:"",
    modelo:"",
    tipo_id:""
  })
  const Imagenes = reactive<ImagenesForm[]>([])
  const DetallesGenerales = reactive<OrdenServicioForm>({
    id:undefined,
    orden_seguimiento: '',
    orden_opcional: '',
    ubicacion: '',
    tipo_id:7,
    modulo_orden:'',
    vehiculo_concepto_id: null,
    empresa_id: null,
    cliente_id:null,
    estimacion: sumarDiasSinDomingo(new Date(new Date().setHours(12,0)),2),
    kilometraje: null,
    gasolina: '',
    telefono: null,
    administrador: '',
    jefe: '',
    trabajador: '',
    tecnico: '',
    descripcion_mo: '',
    indicaciones_cliente: '',
    garantia:'LO ESTIPULADO EN EL CONTRATO',
    observaciones: 'DE ACUERDO A LO DIFICIL DE LA FALLA PARA SU REPARACION',//tiempo de entrega
  });

  const Inventario=reactive<InventarioForm>
  ({
    id:undefined,
    DetallesGeneralesId:undefined,
    llanta:"",
    cables:"",
    estuche:"",
    llave_tuerca:"",
    triangulo:"",
    tarjeta_circulacion:"",
    cubreruedas:"",
    candado_rueda:"",
    extinguidor:"",
    gato:"",
    placas:"",
  })
  const Pintura=reactive<PinturaForm>
  ({
    id:undefined,
    DetallesGeneralesId:undefined,
    decolarada:"",
    color_desigual:"",
    rayonnes:"",
    grietas:"",
    golpes:"",
    emblemas:"",
    logos:"",
    rociado:"",
    granizo:"",
    lluvia:"",
  })
  const CondicionesInteriores=reactive<CondicionesInterioresForm>
  ({
    id:undefined,
    DetallesGeneralesId:undefined,
    puerta_izq_f:"",
    puerta_izq_t:"",
    puerta_der_f:"",
    puerta_der_t:"",
    asiento_izq_f:"",
    asiento_izq_t:"",
    asiento_der_f:"",
    asiento_der_t:"",
    consola:"",
    claxon:"",
    tablero:"",
    quemacocos:"",
    toldo:"",
    elevadores:"",
    luces:"",
    seguros:"",
    climatizador:"",
    radio:"",
    retrovisor:"",
    tapetes:"",
  })
  const CondicionesExteriores=reactive<CondicionesExterioresForm>
  ({
    id:undefined,
    DetallesGeneralesId:undefined,
    antena_radio:"",
    estribos:"",
    antena_telefono:"",
    guardafangos:"",
    antena_cb:"",
    parabrisas:"",
    alarma:"",
    limpiaparabrisas:"",
    luces:"",
    espejos_laterales:""
  })
  const KeysOptional=['orden_opcional','orden_seguimiento'];

  const ImageVehiculoEntrada = ref<InstanceType<typeof ZDCanvas> | null>(null);
  const ImageFirmaEntrada = ref<InstanceType<typeof ZDCanvas> | null>(null);

  const SaveCarAndFirma = async()=>{
    const carro=await ImageVehiculoEntrada.value?.getCanvasBlob();
    const existingIndexCarro = Imagenes.findIndex(img => img.tipo_id === 1);
    if(carro != null){
      if (existingIndexCarro !== -1) {
        Imagenes[existingIndexCarro].image = carro;
      } else {
        Imagenes.push({tipo_id:1,image:carro});
      }
    } else {
      if (existingIndexCarro !== -1) {
        Imagenes.splice(existingIndexCarro, 1);
      }
    }
    const firma=await ImageFirmaEntrada.value?.getCanvasBlob();
    const existingIndexFirma = Imagenes.findIndex(img => img.tipo_id === 2);
    if(firma != null){
      if (existingIndexFirma !== -1) {
        Imagenes[existingIndexFirma].image = firma;
      } else {
        Imagenes.push({tipo_id:2,image:firma});
      }
    } else {
      if (existingIndexFirma !== -1) {
        Imagenes.splice(existingIndexFirma, 1);
      }
    }
  }


  const buttonconfirm=computed<buttonconfirmed>(()=>{ 
    return {
      text:'Crear Presupuesto',
      classname:'bg-blue-600 text-white',
      onClick:async ()=>{
        await SaveCarAndFirma()
      },
      disabled:Object.entries(DetallesGenerales)
      .filter(([key]) => !KeysOptional.includes(key))
      .some(([_,value]) => value === null || value === '')}})
  
  watch(()=>Economico.tipo_id,(val)=>{
    if(val !== ""){
      prueba()
    }
  })
  const prueba = async () => {
    try {
      const response = await axios.get(route('image.tipo.vehiculo', { type: Economico.tipo_id }), {
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
      <InputBasic id="ordenseguimiento" label="Orden De Seguimiento" type="text" v-model="DetallesGenerales.orden_seguimiento" placeholder="Automatico o Ingresar"/>
      <InputBasic id="ordenopcional" label="Orden Opcional" type="text" v-model="DetallesGenerales.orden_opcional" placeholder="Opcional"/>
      <Combobox id="ubicacion" label="Ubicacion" v-model="DetallesGenerales.ubicacion" endpoint="Combobox.ubicaciones" placeholder="Buscar o Crear"/>
      <Select label="Tipo De Presupuesto"  v-model="DetallesGenerales.tipo_id" id="presupuestotipo"  :options="optionstipos"></Select>
      <Select label="Modulo Orden"  v-model="DetallesGenerales.modulo_orden" id="modulooreden" :canempty="true" :options="modulosdisponibles"></Select>
      <Select2 
        :new_option="vehiculoconcepto" 
        :extraparams="{'id_modulo':DetallesGenerales.modulo_orden}" 
        label="Vehiculo De Los Conceptos" 
        endpoint="Select2.Vehiculos.Conceptos.Modulos" 
        v-model="DetallesGenerales.vehiculo_concepto_id" 
        id="presupuestovehiculoconcepto" 
        placeholder="Buscar Cliente" 
        :searchable="false"
        :clear="false"
      />
    </div>
    <Subtitle>Datos Cliente</Subtitle>
    <div class="grid sm:grid-cols-2 gap-2">
      <Select2 :new_option="empresa" label="Empresa" id="presupuestoempresa" endpoint="Select2.Empresas" v-model="DetallesGenerales.empresa_id" placeholder="Buscar Empresas"/>
      <Select2 :new_option="cliente" label="Cliente" endpoint="Select2.Empresas" v-model="DetallesGenerales.cliente_id" id="presupuestoempresa" placeholder="Buscar Cliente"/>
    </div>
    <Subtitle>Datos Vehiculo</Subtitle>
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-2 mt-2">
      <div class="flex flex-col sm:flex-row gap-2">
        <Combobox endpoint="Combobox.Vehiculo.Economico" label="Economico" id="economico" 
          v-model="Economico.economico"  
          placeholder="Buscar o Crear Economico" 
          :timeout="1"/>
        <Combobox endpoint="Combobox.Vehiculo.Placas" label="Placas" id="placas" v-model="Economico.placas"  placeholder="Buscar o Crear PLacas"/>
      </div>
      <InputBasic id="Vin" label="Vin" type="text" v-model="Economico.vin" placeholder="Ej.JJSOE18P388988750 "/>
      <InputBasic id="Año" label="Año" type="number" v-model="Economico.anio"  placeholder="ej. 2024"/>
      <InputBasic id="Marca" label="Marcas" type="text" v-model="Economico.marca" classname="uppercase" placeholder="ej. AUDI"/>
      <InputBasic id="Modelo" label="Modelo" type="text" v-model="Economico.modelo" classname="uppercase" placeholder="ej. A3"/>
      <TiposVehiculos label="Tipo De Vehiculo" id="tipovehiculo" v-model="Economico.tipo_id" />
    </div>
    <Subtitle>Datos De Ingreso</Subtitle>
    <div class="grid sm:grid-cols-2 md:grid-cols-4 gap-2" >
      <InputBasic id="telefono" label="Telefono" type="number" v-model="DetallesGenerales.telefono"  placeholder="ej. 2024"/>
      <Datapicker label="Fecha Estimada" v-model="DetallesGenerales.estimacion" :clearable="false" :time="true" :range="false" class="w-full sm:col-span-2 md:col-span-1"/>
      <InputBasic id="kilometraje" label="Kilometraje" type="number" v-model="DetallesGenerales.kilometraje" placeholder="ej. 392.31"/>
      <Select id="gasolina" :canempty="true" v-model="DetallesGenerales.gasolina" label="Gasolina" :options="optionsgasolima"></Select>
      <div class="md:col-span-4 sm:col-span-2 flex md:flex-row gap-2" >
        <ZDCanvas class="w-[70%]" ref="ImageVehiculoEntrada" title="Detalles Del Vehiculo" strokecolor="red"></ZDCanvas>
        <ZDCanvas class="w-[30%]" ref="ImageFirmaEntrada" title="Firma"></ZDCanvas>
      </div>
    </div>
    <Subtitle>Empleados Encargados</Subtitle>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-2" >
      <Combobox endpoint="Combobox.Administradores_Trasporte" label="Administrador de Trasportes" id="administradortrasporte" v-model="DetallesGenerales.administrador"  placeholder="Buscar o Crear "/>
      <Combobox endpoint="Combobox.Jefes_Procesos" label="Jefe De Procesos" id="jefeproceso" v-model="DetallesGenerales.jefe"  placeholder="Buscar o Crear"/>
      <Combobox endpoint="Combobox.Trabajadores" label="Trabajador" id="trabajador" v-model="DetallesGenerales.trabajador"  placeholder="Buscar o Crear"/>
      <Combobox endpoint="Combobox.Tecnicos" label="Tecnico" id="tecnico" v-model="DetallesGenerales.tecnico"  placeholder="Buscar o Crear"/>
    </div>
    <Subtitle>Notas</Subtitle>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-2" >
      <Textarea id="indicacionescliente" label="Indicaciones Del Cliente" v-model="DetallesGenerales.indicaciones_cliente" placeholder="Escribe las indicaciones del cliente aqui..." classname="h-24"/>
      <Textarea id="notasmecanico" label="Descripcion Mano De Obra" v-model="DetallesGenerales.descripcion_mo" placeholder="Escribe las notas del mecanico aqui..." classname="h-24"/>
      <Textarea id="observaciones" label="Garantia" v-model="DetallesGenerales.garantia" placeholder="Escribe las observaciones aqui..." classname="h-24"/>
      <Textarea id="descripcionmo" label="Tiempo de Entrega" v-model="DetallesGenerales.observaciones" placeholder="Escribe la descripcion de la mano de obra aqui..." classname="h-24"/>
    </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
      <div>
        <Subtitle>Inventario Equipo</Subtitle>
        <div class="grid sm:grid-cols-2 2xl:grid-cols-3">
          <Checkbox v-for="(item,index) in InventarioInputs" value='1' :key="'inventario-'+index" :checked="Inventario[index] ==='1'" :label="item"/>
        </div>
      </div>
      <div>
        <Subtitle>Condiciones Pintura</Subtitle>
        <div class="grid sm:grid-cols-2 2xl:grid-cols-3">
          <Checkbox v-for="(item,index) in PinturaInputs" value='1' :key="'inventario-'+index" :checked="Pintura[index] ==='1'" :label="item"/>
        </div>
      </div>
    </div>
    <div class="border-2 rounded-md p-2">
      <Subtitle>Condiciones Equipo Interior</Subtitle>
      <div class="grid sm:grid-cols-2 gap-2">
        <div >
          <Subtitle bg="bg-[--color4]">Puertas</Subtitle>
          <div class="grid md:grid-cols-2 2xl:grid-cols-4 gap-2">
            <Select v-for="(item,index) in CondicionesInterioresInputs['PANALES DE PUERTA']" 
            :label="item" 
            :key="'inventario-'+index" 
            v-model="CondicionesInteriores[index]" 
            :options="optionsequipo">
          </Select>
          </div>
        </div>
        <div>
          <Subtitle bg="bg-[--color4]">Asientos</Subtitle>
          <div class="grid md:grid-cols-2 2xl:grid-cols-4 gap-2">
            <Select v-for="(item,index) in CondicionesInterioresInputs['ASIENTOS']" 
            :label="item" 
            :key="'inventario-'+index" 
            v-model="CondicionesInteriores[index]" 
            :options="optionsequipo">
          </Select>
          </div>
        </div>
      </div>
      <div>
        <Subtitle bg="bg-[--color4]">Otros</Subtitle>
        <div class="grid sm:grid-cols-2 md:grid-cols-4 2xl:grid-cols-6 gap-2">
            <Select v-for="(item,index) in CondicionesInterioresInputs['OTROS']" 
            :label="item" 
            :key="'inventario-'+index" 
            v-model="CondicionesInteriores[index]" 
            :options="optionsequipo">
          </Select>
        </div>
      </div>
    </div>
    <div>
      <Subtitle>Condiciones Equipo Exterior</Subtitle>
      <div class="grid gap-2 sm:grid-cols-3 xl:grid-cols-5 ">
        <Select v-for="(item,index) in CondicionesExterioresInputs" 
          :label="item" 
          :key="'inventario-'+index" 
          v-model="CondicionesExteriores[index]"
          :options="optionsequipo">
        </Select>
      </div>
    </div>
  </BaseModal>
</template>
