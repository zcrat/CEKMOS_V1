fd<!-- ModalExample.vue -->
<script setup lang="ts">
  import InputBasic from '../Inputs/form/InputBasic.vue'
  import Textarea from '../Inputs/form/Textarea.vue'
  import BaseModal from '@/components/Zcrat/modals/BaseModal.vue'
  import { ref, watch ,reactive, onMounted,computed} from 'vue' 
  import Subtitle from '@/components/Zcrat/Elements/Subtitle.vue';
  import {type option} from '@/types/generales'
  import {type buttonconfirmed} from '@/types/modals'
  import Combobox from '@/components/Zcrat/Elements/ZdCombobox.vue'
  import Datapicker from '@/components/Zcrat/Elements/ZDDataPicker.vue';
  import Select from '@/components/Zcrat/Elements/Select.vue';
  import Select2 from '@/components/Zcrat/Elements/Select2.vue';
  import GetNivelesGasolina from '@/utils/functions/select/NivelesGasolina';
  import GetModulosDisponibles  from '@/utils/functions/select/ModulosCortana';
  import { sumarDiasSinDomingo } from '@/utils/functions/generales/fechas';
  import ZDCanvas from '../Elements/ZDCanvas.vue'
  import axios from 'axios' 
  import MyBasicToast from "@/utils/ToastNotificationBasic";
  import TiposVehiculos from '../Forms/TiposVehiculos.vue';
  import GetStatusPerCategory from '@/utils/functions/select/StatusPerCategory'
  import Checkbox from '../Inputs/form/Checkbox.vue';
  import OptionsCondicionesEquipo from '../Elements/OptionsCondicionesEquipo.vue';
  import Button from '../Inputs/Button.vue';

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
    vehiculo_concepto_id: option| null,
    empresa:option|null,
    cliente:option|null,
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
    decolarada:"1"|"0",
    color_desigual:"1"|"0",
    rayonnes:"1"|"0",
    grietas:"1"|"0",
    golpes:"1"|"0",
    emblemas:"1"|"0",
    logos:"1"|"0",
    rociado:"1"|"0",
    granizo:"1"|"0",
    lluvia:"1"|"0",
  }
  export interface InventarioForm{
    id?:number,
    DetallesGeneralesId?:number,
    llanta:"1"|"0",
    cables:"1"|"0",
    estuche:"1"|"0",
    llave_tuerca:"1"|"0",
    triangulo:"1"|"0",
    tarjeta_circulacion:"1"|"0",
    cubreruedas:"1"|"0",
    candado_rueda:"1"|"0",
    extinguidor:"1"|"0",
    gato:"1"|"0",
    placas:"1"|"0",
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
  const optionsgasolima=ref<option[]>([])
  const optionsequipo=ref<option[]>([])
  const modulosdisponibles=ref<option[]>([])
  const imagepreview=ref<string>('')
  const optionstipos=[
    {value:5,label:'Correctivo'},
    {value:6,label:'Preventivo'},
    {value:7,label:'Ambos'}
  ];
  const OpcionesCondicionesEquipo=[
    {value:'D',label:'Dañada'},
    {value:'O',label:'Operacional'},
    {value:'F',label:'Falta Objeto'},
    {value:'R',label:'Reparacion Necesaria'},
    {value:'NA',label:'No Aplica'},
    {icon:'fa-solid fa-check',label:'Sin Daño Visible'},
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

  
  const updateVisibility = () => {
    emit('close')
  }
  onMounted(async () => {console.log('montado')
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
  const Database: OrdenServicioForm ={
    id:undefined,
    orden_seguimiento: '',
    orden_opcional: '',
    ubicacion: '',
    tipo_id:7,
    modulo_orden:'',
    vehiculo_concepto_id: null,
    empresa: null,
    cliente:null,
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
  }
  const DetallesGenerales = reactive<OrdenServicioForm>(Database);

  const Inventario=reactive<InventarioForm>
  ({
    id:undefined,
    DetallesGeneralesId:undefined,
    llanta:"0",
    cables:"0",
    estuche:"0",
    llave_tuerca:"0",
    triangulo:"0",
    tarjeta_circulacion:"0",
    cubreruedas:"0",
    candado_rueda:"0",
    extinguidor:"0",
    gato:"0",
    placas:"0",
  })
  const Pintura=reactive<PinturaForm>
  ({
    id:undefined,
    DetallesGeneralesId:undefined,
    decolarada:"0",
    color_desigual:"0",
    rayonnes:"0",
    grietas:"0",
    golpes:"0",
    emblemas:"0",
    logos:"0",
    rociado:"0",
    granizo:"0",
    lluvia:"0",
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
  const KeysOptional=['orden_opcional','orden_seguimiento','id'];

  const ImageVehiculoEntrada = ref<InstanceType<typeof ZDCanvas> | null>(null);
  const ImageFirmaEntrada = ref<InstanceType<typeof ZDCanvas> | null>(null);
  const LoadImages = ref<HTMLInputElement | null>(null)
  const CanEditImages = ref<boolean>(true)

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
      .some(([_,value]) => value === null || value === '') 
      ||
      Object.entries(Economico)
      .filter(([key]) => !['id'].includes(key))
      .some(([_,value]) => value === null || value === '')
      ||
      Object.entries(CondicionesInteriores)
      .filter(([key]) => !['id','DetallesGeneralesId'].includes(key))
      .some(([_,value]) => value === null || value === '')
      ||
      Object.entries(CondicionesExteriores)
      .filter(([key]) => !['id','DetallesGeneralesId'].includes(key))
      .some(([_,value]) => value === null || value === '')
      ||
      Imagenes.filter(item=>item.tipo_id ===3).length < 6
    }

    })  
  
  watch(()=>Economico.tipo_id,(val)=>{
    if(val !== ""){
      prueba()
    }
  })
  watch(()=>DetallesGenerales.modulo_orden,()=>{
    DetallesGenerales.vehiculo_concepto_id=null
  })
  watch(()=>DetallesGenerales.empresa,()=>{
    DetallesGenerales.cliente=null
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
  const SaveImagesEvidencia = (event: Event) => {
    const target = event.target as HTMLInputElement
    if (target.files) {
      if (Array.from(target.files).some(file => !file.type.startsWith('image/'))) {
        MyBasicToast.error('Solo se permiten archivos de imagen');
          return;
      } 
      Array.from(target.files).forEach(file => {
        
        Imagenes.push({ tipo_id: 3, image: file })
      })
    }
    target.value = '';
  }
  function CovertBlobToURL(file: Blob) {
    return window.URL.createObjectURL(file)
  }
  function DeleteImage(index:number) {
    const registro=Imagenes[index];
    if(registro){
      if(registro.ids?.id){
        // Aquí podrías hacer una petición al servidor para eliminar la imagen usando el ID
        // Por ejemplo: await axios.delete(`/api/imagenes/${registro.id}`);
      }else{
        Imagenes.splice(index, 1);
      }
    }
  }
  function DeleteImagesNew() {
    const imagenesNuevas = Imagenes.filter(img => !img.ids?.id);
    if (imagenesNuevas.length > 0) {
      imagenesNuevas.forEach((img) => {
        const index = Imagenes.findIndex(i => i.image === img.image);
        if (index !== -1) {
          Imagenes.splice(index, 1);
        }
      });
    }else{
      MyBasicToast.error('No hay imágenes nuevas para eliminar');
    }
  }
  function GetDataVehiculoEconomico() {
    if(Economico.economico){
      axios.get(route('Vehiculo.Get.Datos'), {
        params: { search: Economico.economico ,filtro:'economico'}
      })
      .then(response => {
        const data = response.data.datos;
        if (data) {
          Economico.placas = data.placas;
          Economico.vin = data.vin;
          Economico.anio = data.anio;
          Economico.marca = data.modelo.marca.descripcion;
          Economico.modelo = data.modelo.descripcion;
          Economico.tipo_id = data.tipo_id;
        } else {
          if(!Economico.placas){
            MyBasicToast.warning('Crearas un nuevo vehículo, no se encontraron datos con el economico proporcionado');
          }
        }
      })
      .catch(error => {
        console.error('Error al obtener los datos del vehículo:', error);
      });
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
        :params="{'id_modulo':DetallesGenerales.modulo_orden}" 
        label="Vehiculo De Los Conceptos" 
        endpoint="Select2.Vehiculos.Conceptos.Modulos" 
        v-model="DetallesGenerales.vehiculo_concepto_id" 
        :empty_message="DetallesGenerales.modulo_orden? 'Sin Resultados':'Selecciona Un Modulo'" 
        placeholder="Buscar Vehiculo" 
        :cleareble="true"
      />
    </div>
    <Subtitle>Datos Cliente</Subtitle>
    <div class="grid sm:grid-cols-2 gap-2">
      <Select2 
        label="Empresa" 
        id="presupuestoempresa" 
        endpoint="Select2.Empresas" 
        v-model="DetallesGenerales.empresa" 
        placeholder="Buscar Empresas"
      />
      <Select2   
      label="Cliente" 
      :params="{'empresa_id':DetallesGenerales.empresa?.value}" 
      endpoint="Select2.Clientes" 
      :empty_message="DetallesGenerales.empresa ? 'Sin Resultados' : 'Selecciona una empresa'"
      v-model="DetallesGenerales.cliente" 
      id="presupuestoempresa" 
      placeholder="Buscar Cliente"/>
    </div>
    <Subtitle>Datos Vehiculo</Subtitle>
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-2 mt-2">
      <div class="flex flex-col sm:flex-row gap-2">
        <Combobox 
          endpoint="Combobox.Vehiculo.Economico" 
          label="Economico" id="economico" 
          v-model="Economico.economico"
          :OnBlur="GetDataVehiculoEconomico"
          placeholder="Buscar o Crear Economico" 
          :timeout="1"
          />
        <Combobox endpoint="Combobox.Vehiculo.Placas" label="Placas" id="placas" v-model="Economico.placas"  placeholder="Buscar o Crear PLacas" />
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
      <div class="md:col-span-4 sm:col-span-2 flex flex-col md:flex-row gap-2" >
        <ZDCanvas class="md:w-[70%]" ref="ImageVehiculoEntrada" title="Detalles Del Vehiculo" strokecolor="red"></ZDCanvas>
        <ZDCanvas class="md:w-[30%]" classnamedivcanvas="w-full h-[10rem]" ref="ImageFirmaEntrada" title="Firma"></ZDCanvas>
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
        <div class="grid grid-cols-1 md:grid-cols-2 gap-2 mt-2">
      <div class="border-2 rounded-md p-2">
        <Subtitle>Inventario Equipo</Subtitle>
        <div class="grid sm:grid-cols-2 2xl:grid-cols-3">
          <Checkbox v-for="(item,index) in InventarioInputs" value='1' :key="'inventario-'+index" :checked="Inventario[index] ==='1'" :label="item" @update:checked="()=>{Inventario[index] = Inventario[index] === '1' ? '0' : '1'}"/>
        </div>
      </div>
      <div class="border-2 rounded-md p-2">
        <Subtitle>Condiciones Pintura</Subtitle>
        <div class="grid sm:grid-cols-2 2xl:grid-cols-3">
          <Checkbox v-for="(item,index) in PinturaInputs" value='1' :key="'inventario-'+index" :checked="Pintura[index] ==='1'" :label="item" @update:checked="()=>{Pintura[index] = Pintura[index] === '1' ? '0' : '1'}"/>
        </div>
      </div>
    </div>
    <div class="border rounded-md py-2 my-2 flex gap-2 justify-around flex-wrap">
      
      <div v-for="(item,index) in OpcionesCondicionesEquipo" :key="'leyenda-'+index" class="flex items-center gap-1">
        <font-awesome-icon :icon="item.icon" v-if="item.icon"></font-awesome-icon><p v-if="item.value">{{ item.value }}</p>=<p>{{item.label}}</p>
      </div>
    </div>
    <div class="border-2 rounded-md p-2">
      <Subtitle>Condiciones Equipo Interior</Subtitle>
      <div class="grid sm:grid-cols-2 gap-2">
        <div >
          <Subtitle bg="bg-[--color4]">Puertas</Subtitle>
          <div class=" justify-center items-center grid lg:grid-cols-2 2xl:grid-cols-4 gap-2">
            <OptionsCondicionesEquipo v-for="(item,index) in CondicionesInterioresInputs['PANALES DE PUERTA']" 
            :label="item" 
            :key="'inventario-'+index" 
            v-model="CondicionesInteriores[index]" 
            :options="optionsequipo">
          </OptionsCondicionesEquipo>
          </div>
        </div>
        <div>
          <Subtitle bg="bg-[--color4]">Asientos</Subtitle>
          <div class="flex flex-col items-center lg:grid lg:grid-cols-2 2xl:grid-cols-4 gap-2">
            <OptionsCondicionesEquipo v-for="(item,index) in CondicionesInterioresInputs['ASIENTOS']" 
            :label="item" 
            :key="'inventario-'+index" 
            v-model="CondicionesInteriores[index]" 
            :options="optionsequipo">
          </OptionsCondicionesEquipo>
          </div>
        </div>
      </div>
      <div>
        <Subtitle bg="bg-[--color4]">Otros</Subtitle>
        <div class="grid sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 2xl:grid-cols-6 gap-2">
            <OptionsCondicionesEquipo v-for="(item,index) in CondicionesInterioresInputs['OTROS']" 
            :label="item" 
            :key="'inventario-'+index" 
            v-model="CondicionesInteriores[index]" 
            :options="optionsequipo">
          </OptionsCondicionesEquipo>
        </div>
      </div>
    </div>
    <div class="border-2 rounded-md p-2 mt-2">
      <Subtitle>Condiciones Equipo Exterior</Subtitle>
      <div class="grid gap-2 sm:grid-cols-2  md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 ">
        <OptionsCondicionesEquipo  :key="'equipo-exterior-'+index"  v-for="(item,index) in CondicionesExterioresInputs" :label="item" v-model="CondicionesExteriores[index]"/>
      </div>
    </div>
    <div class="pb-2 border-2 p-2 rounded  mt-2">
      <Subtitle>Evidencias</Subtitle>
      <div class="flex flex-col sm:flex-row gap-2 w-full">
        <div class="flex flex-row sm:flex-col gap-2" v-if="CanEditImages">
          <input type="file" name="LoadImages" id="LoadImages" ref="LoadImages" multiple style="position: absolute; left: -9999px;" accept="image/*" @change="SaveImagesEvidencia"/>
          <Button text="Tomar Fotos"  @click="()=>{LoadImages?.click()}" />
          <Button text="Eliminar Fotos" @click="DeleteImagesNew"  type="delete" v-if="Imagenes.some(img => img.tipo_id === 3)"/>
        </div>
        <div :class="'overflow-x-auto flex gap-2 flex-row'">
          <div :key="index" v-for="(value,index) in Imagenes.filter(item=>item.tipo_id ===3)" class="border-2 rounded-md border-gray-700 p-2 w-fit flex flex-col justify-end">
             <img :src="CovertBlobToURL(value.image)" class="max-w-[200px] max-h-[200px]" @click="imagepreview=CovertBlobToURL(value.image)" />
            <Button text="Eliminar"  type="delete" @click="DeleteImage(index)" v-if="CanEditImages"/>
          </div>
        </div>
      </div>
      <div v-if="imagepreview != ''" class='fixed inset-0 bg-black/50 flex items-center justify-center z-50'  @click="imagepreview=''">
        <img :src="imagepreview" alt="imagen de evidencia" class="max-w-[90vw] max-h-[90vh] rounded-md shadow-lg"/>
      </div>
    </div>
  </BaseModal>
</template>
