<script setup lang="ts">
  import InputBasic from '../Inputs/form/InputBasic.vue'
  import Textarea from '../Inputs/form/Textarea.vue'
  import BaseModal from '@/components/Zcrat/modals/BaseModal.vue'
  import { ref, watch ,reactive, onMounted,computed, nextTick} from 'vue' 
  import Subtitle from '@/components/Zcrat/Elements/Subtitle.vue';
  import {type option} from '@/types/generales'
  import {type buttonconfirmed} from '@/types/modals'
  import Combobox from '@/components/Zcrat/Elements/ZdCombobox.vue'
  import Datapicker from '@/components/Zcrat/Elements/ZDDataPicker.vue';
  import Select from '@/components/Zcrat/Elements/Select.vue';
  import Select2 from '@/components/Zcrat/Elements/Select2.vue';
  import GetNivelesGasolina from '@/utils/functions/select/NivelesGasolina';
  import GetModulosDisponibles  from '@/utils/functions/select/ModulosCortana';
  import ZDCanvas, { StrokesArray } from '../Elements/ZDCanvas.vue'
  import TiposVehiculos from '../Forms/TiposVehiculos.vue';
  import GetStatusPerCategory from '@/utils/functions/select/StatusPerCategory'
  import {EconomicoForm,OrdenServicioForm,FilesForm,CondicionesInterioresForm,CondicionesExterioresForm, PinturaForm, InventarioForm, DetallesGeneralesBaseProps} from '@/types/OrdenServicio'
  import {optionstipos} from '@/utils/variables/options'
  import {DetallesGeneralesBase,CondicionesInterioresBase,CondicionesExterioresBase,EconomicoBase, PinturaBase, InventarioBase} from '@/utils/variables/ordenservicio'
  import {GetImageTipoVehiculo, SaveCarAndFirma } from '@/utils/functions/ordenservicio';
  import ImagenesEvidencias from './partes/ordenservicio/ImagenesEvidencias.vue';
  import CondicionesInterioresTemplate from './partes/ordenservicio/CondicionesInteriores.vue';
  import CondicionesExterioresTemplate from './partes/ordenservicio/CondicionesExteriores.vue';
  import ValuesCondicionesEquipo from './partes/ordenservicio/ValuesCondicionesEquipo.vue';
  import InventarioTemplate from './partes/ordenservicio/InventarioTemplate.vue';
  import PinturaTemplate from './partes/ordenservicio/PinturaTemplate.vue';
  import { ZdAlert } from '@/utils/ZdAlert';
  import Loading from '../Elements/Loading.vue';
  import { Create } from '@/services/orden-servicio/crud';
  import VehiculoModal from './VehiculoModal.vue';
  import MyBasicToast from '@/utils/ToastNotificationBasic';
  import {type Vehiculo as VehiculoProps} from '@/types/generales'
  import axios from 'axios';
  const emit = defineEmits(['close'])
  const props = defineProps<{show: boolean}>()
  const optionsgasolima=ref<option[]>([])
  const optionsequipo=ref<option[]>([])
  const modulosdisponibles=ref<option[]>([])
  const ImagenesCanvas=ref<{'firma'?:StrokesArray , 'carro'?:StrokesArray}>({})
  const OpenModal = ref<null|1>(null);
  const CanEditImages = ref<boolean>(true)
  const updateVisibility = () => {
    emit('close')
  }
  onMounted(async () => {console.log('montado')
    optionsgasolima.value = await GetNivelesGasolina();
    optionsequipo.value = await GetStatusPerCategory(11);
    modulosdisponibles.value=await GetModulosDisponibles();
  });
  const Vehiculo = reactive<EconomicoForm>(EconomicoBase)
  const Imagenes = ref<FilesForm[]>([])
  const loading = ref<boolean>(false)
  const DetallesGenerales = reactive<DetallesGeneralesBaseProps>(DetallesGeneralesBase);
  const CondicionesInteriores=ref<CondicionesInterioresForm>(CondicionesInterioresBase)
  const CondicionesExteriores=ref<CondicionesExterioresForm>(CondicionesExterioresBase)
  const Pintura=ref<PinturaForm>({...PinturaBase})
  const Inventario=ref<InventarioForm>({...InventarioBase})
  const ImageVehiculoEntrada = ref<InstanceType<typeof ZDCanvas> | null>(null);
  const ImageFirmaEntrada = ref<InstanceType<typeof ZDCanvas> | null>(null);
  const buttonconfirm=computed<buttonconfirmed>(()=>{ 
    return {
      text:'Crear Orden De Servicio',
      classname:'bg-blue-600 text-white',
      onClick:async ()=>{
        console.log('click')
        console.log(Imagenes.value)
        ImagenesCanvas.value.carro=ImageVehiculoEntrada.value?.GetStrokes();
        ImagenesCanvas.value.firma=ImageFirmaEntrada.value?.GetStrokes();
        await SaveCarAndFirma({Carro:ImageVehiculoEntrada.value,Firma:ImageFirmaEntrada.value,Imagenes:Imagenes.value})
        const confirm=await ZdAlert({});
        if(!confirm){return}
        loading.value=true
        const Form:OrdenServicioForm={
          orden_seguimiento: DetallesGenerales.orden_seguimiento,
          orden_opcional: DetallesGenerales.orden_opcional,
          ubicacion: DetallesGenerales.ubicacion,
          tipo_presupuesto_id: DetallesGenerales.tipo_id,
          modulo_orden_id: Number(DetallesGenerales.modulo_orden),
          vehiculo_concepto_id: DetallesGenerales.vehiculo_concepto_id?.value,
          empresa_id: DetallesGenerales.empresa?.value,
          cliente_id: DetallesGenerales.cliente?.value,
          vehiculo_id: DetallesGenerales.vehiculo?.value,
          telefono: DetallesGenerales.telefono ?? 0,
          estimacion: DetallesGenerales.estimacion,
          kilometraje: DetallesGenerales.kilometraje ?? 0,
          gasolina: Number(DetallesGenerales.gasolina) ?? 0,
          administrador: DetallesGenerales.administrador,
          jefe: DetallesGenerales.jefe,
          trabajador: DetallesGenerales.trabajador,
          tecnico: DetallesGenerales.tecnico,
          indicaciones_cliente: DetallesGenerales.indicaciones_cliente,
          descripcion_mo: DetallesGenerales.descripcion_mo,
          garantia: DetallesGenerales.garantia,
          observaciones: DetallesGenerales.observaciones,
          imagenes_evidencia: Imagenes.value.map(img=>img.tipo_id === 3 ? img.file : null).filter(img=>img !== null) as File[],
          carro: Imagenes.value.find((item) => item.tipo_id === 1) ? Imagenes.value.find((item) => item.tipo_id === 1)!.file : null,
          firma: Imagenes.value.find((item) => item.tipo_id === 2) ? Imagenes.value.find((item) => item.tipo_id === 2)!.file : null,
          condiciones_exteriores: CondicionesExteriores.value,
          condiciones_interiores: CondicionesInteriores.value,
          pintura: Pintura.value,
          inventario: Inventario.value
        }
        const response=await Create(Form);
        if(response.status){
          MyBasicToast.success(response.data.message??'Orden De Servicio Creada Con Exito')
          updateVisibility();
        }else{
          MyBasicToast.error(response.data.message??'Error Al Crear La Orden De Servicio' )
        }

        await new Promise(resolve => setTimeout(resolve, 1000));
        loading.value=false

      },
      disabled: 1!=1 &&(
        !DetallesGenerales.ubicacion ||
        !DetallesGenerales.tipo_id ||
        !DetallesGenerales.modulo_orden ||
        !DetallesGenerales.vehiculo ||
        !DetallesGenerales.vehiculo_concepto_id ||
        !DetallesGenerales.empresa ||
        !DetallesGenerales.cliente ||
        !DetallesGenerales.telefono ||
        !DetallesGenerales.estimacion ||
        !DetallesGenerales.kilometraje ||
        !DetallesGenerales.gasolina||
        !DetallesGenerales.administrador ||
        !DetallesGenerales.jefe ||
        !DetallesGenerales.trabajador ||
        !DetallesGenerales.tecnico ||
        !DetallesGenerales.indicaciones_cliente ||
        !DetallesGenerales.descripcion_mo ||
        !DetallesGenerales.garantia ||
        !DetallesGenerales.observaciones ||
        Imagenes.value.filter(img=>img.tipo_id === 3).length === 0 ||
        Object.entries(CondicionesInteriores.value).filter(([key]) => !['id','DetallesGeneralesId'].includes(key)).
        some(([_,value]) => value === null || value === '')||
        Object.entries(CondicionesInteriores.value).filter(([key]) => !['id','DetallesGeneralesId'].includes(key)).
        some(([_,value]) => value === null || value === '')
      )
    }

  })  
  watch(()=>Vehiculo.tipo_id,(val)=>{
    if(val){GetImageTipoVehiculo({Canvas:ImageVehiculoEntrada.value, Tipo:val})}
  })
  watch(()=>DetallesGenerales.modulo_orden,()=>{
    DetallesGenerales.vehiculo_concepto_id=null
  })
  watch(()=>DetallesGenerales.empresa,()=>{
    DetallesGenerales.cliente=null
  })
  watch(()=>DetallesGenerales.vehiculo,()=>{
    if(DetallesGenerales.vehiculo?.value){
      Read();
    }else{
      Vehiculo.placas='';
      Vehiculo.economico='';
      Vehiculo.vin='';
      Vehiculo.anio='';
      Vehiculo.tipo_id=null;
      Vehiculo.color='';
      Vehiculo.modelo='';
      Vehiculo.marca='';
    }
  })
  const OpenOtherModal= (val:1)=>{
    ImagenesCanvas.value.carro=ImageVehiculoEntrada.value?.GetStrokes();
    ImagenesCanvas.value.firma=ImageFirmaEntrada.value?.GetStrokes();
    OpenModal.value = val;
  }
  const CloseOtherModal= ()=>{
    OpenModal.value = null;
  }
  const Read = async () => {
    try {
      const response = await axios.get(route('Vehiculo.Find'),{params:{id:DetallesGenerales.vehiculo?.value} })
      const data:VehiculoProps=response.data.vehiculo;
      Vehiculo.placas=data.placas;
      Vehiculo.economico=data.economico;
      Vehiculo.vin=data.vin;
      Vehiculo.color=data.color?.descripcion ?? 'No Encontrado';
      Vehiculo.anio=data.año ?? '';
      Vehiculo.tipo_id=Number(data.tipo_id);
      Vehiculo.modelo=data.modelo?.descripcion ?? 'No Encontrado';
      Vehiculo.marca=data.modelo?.marca?.descripcion ?? 'No Encontrado';
    } catch (error: any) {
      console.error('Error:', error)
      emit('close')
    }
  }
  watch([OpenModal,loading],async ()=>{
    if(OpenModal.value === null && loading.value=== false){
      const firmablob=ImagenesCanvas.value.firma;
      const carroblob=ImagenesCanvas.value.carro;
      await nextTick();
      if(firmablob){
        ImageFirmaEntrada.value?.SetStrokes(firmablob) 
      }
      if(carroblob){
        ImageVehiculoEntrada.value?.SetStrokes(carroblob) 
      }
      ImagenesCanvas.value={}
    }
  })
</script>
<template>
  <VehiculoModal
    :show="OpenModal==1"
    @close="CloseOtherModal"
    :id="DetallesGenerales.vehiculo?.value"
    :returnSave="(val)=>{DetallesGenerales.vehiculo={value:val.id??'0',label:(val.economico+'-'+val.placas)}}"
  />
  <BaseModal modaltitle="Nueva Orden De Servicio" 
  :position="'center'"
  :show="props.show && OpenModal === null" 
  @close="updateVisibility" 
  :buttonconfirm="buttonconfirm"
  :loading="loading"
  :textLoading="DetallesGenerales.id ? 'Espere a que termine de actualizar' : 'Espere a que termine de Crear'"
  >
   <template v-if="loading">
    <Loading  text="Espere Un Momento"/>
  </template>
  <template  v-else>
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
      <Select2 
        label="Vehiculo" 
        :buttonNew="()=>{OpenOtherModal(1);}"
        id="Vehiculo" 
        endpoint="Select2.Economico" 
        v-model="DetallesGenerales.vehiculo" 
        placeholder="Buscar por Economico o Placas"
      />
      <template v-if="DetallesGenerales.vehiculo">
        <InputBasic id="Vin" disabled label="Vin" type="text" v-model="Vehiculo.vin" placeholder="Ej.JJSOE18P388988750 "/>
        <InputBasic id="Año" label="Año" type="number" v-model="Vehiculo.anio"  placeholder="ej. 2024"/>
        <InputBasic id="Marca" label="Marcas" type="text" v-model="Vehiculo.marca" classname="uppercase" placeholder="ej. AUDI"/>
        <InputBasic id="Modelo" label="Modelo" type="text" v-model="Vehiculo.modelo" classname="uppercase" placeholder="ej. A3"/>
        <InputBasic id="Color" label="Color" type="text" v-model="Vehiculo.color" classname="uppercase" placeholder="ej. Rojo"/>
        <TiposVehiculos disabled label="Tipo De Vehiculo" id="tipovehiculo" v-model="Vehiculo.tipo_id" />
      </template>
    </div>
    <Subtitle>Datos De Ingreso</Subtitle>
    <div class="grid sm:grid-cols-2 md:grid-cols-4 gap-2" >
      <InputBasic id="telefono" label="Telefono" type="number" v-model="DetallesGenerales.telefono"  placeholder="ej. 4433221100"/>
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
      <InventarioTemplate v-model="Inventario"/>
      <PinturaTemplate v-model="Pintura"/>
    </div>
    <ValuesCondicionesEquipo/>
    <CondicionesInterioresTemplate v-model="CondicionesInteriores"/>
    <CondicionesExterioresTemplate v-model="CondicionesExteriores"/>
    <ImagenesEvidencias v-model:Imagenes="Imagenes" :CanEditImages="CanEditImages"/>
  </template>
 
  </BaseModal>
</template>