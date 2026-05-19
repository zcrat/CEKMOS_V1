<script setup lang="ts">
  import InputBasic from '../Inputs/form/InputBasic.vue'
  import Textarea from '../Inputs/form/Textarea.vue'
  import BaseModal from '@/components/Zcrat/modals/BaseModal.vue'
  import { ref, watch ,reactive, onMounted,computed, nextTick} from 'vue' 
  import Subtitle from '@/components/Zcrat/Elements/Subtitle.vue';
  import {ArrayAsociativo, type option} from '@/types/generales'
  import {type buttonconfirmed} from '@/types/modals'
  import Combobox from '@/components/Zcrat/Elements/ZdCombobox.vue'
  import Datapicker from '@/components/Zcrat/Elements/ZDDataPicker.vue';
  import Select from '@/components/Zcrat/Elements/Select.vue';
  import Select2 from '@/components/Zcrat/Elements/Select2.vue';
  import GetNivelesGasolina from '@/utils/functions/select/NivelesGasolina';
  import GetModulosDisponibles  from '@/utils/functions/select/ModulosCortana';
  import ZDCanvas, { StrokesArray } from '../Elements/ZDCanvas.vue'
  import GetStatusPerCategory from '@/utils/functions/select/StatusPerCategory'
  import {OrdenServicioForm,CondicionesInterioresForm,CondicionesExterioresForm, PinturaForm, InventarioForm, DetallesGeneralesBaseProps} from '@/types/OrdenServicio'
  import {optionstipos} from '@/utils/variables/options'
  import {DetallesGeneralesBase,CondicionesInterioresBase,CondicionesExterioresBase,PinturaBase,InventarioBase} from '@/utils/variables/ordenservicio'
  import {GetImageTipoVehiculo, ImageCanvas } from '@/utils/functions/ordenservicio';
  import ImagenesEvidencias,{type ImagenUpload} from '@/components/Zcrat/modals/partes/ordenservicio/ImagenesEvidencias.vue';
  import CondicionesInterioresTemplate from './partes/ordenservicio/CondicionesInteriores.vue';
  import CondicionesExterioresTemplate from './partes/ordenservicio/CondicionesExteriores.vue';
  import ValuesCondicionesEquipo from './partes/ordenservicio/ValuesCondicionesEquipo.vue';
  import InventarioTemplate from './partes/ordenservicio/InventarioTemplate.vue';
  import PinturaTemplate from './partes/ordenservicio/PinturaTemplate.vue';
  import { ZdAlert } from '@/utils/ZdAlert';
  import Loading from '../Elements/Loading.vue';
  import { CreateorUpdate } from '@/services/orden-servicio/crud';
  import VehiculoModal from './VehiculoModal.vue';
  import MyBasicToast from '@/utils/ToastNotificationBasic';
  import VehiculoForm from '@/components/Zcrat/modals/partes/ordenservicio/VehiculoForm.vue'
  import axios from 'axios';

  const emit = defineEmits(['close'])
  const optionsgasolima=ref<option[]>([])
  const optionsequipo=ref<option[]>([])
  const modulosdisponibles=ref<option[]>([])
  const ImagenesCanvas=ref<{'firma'?:StrokesArray , 'carro'?:StrokesArray,'firma_img'?:File | null , 'carro_img'?:File | null}>({})
  const OpenModal = ref<null|1>(null);
  const resetvalues = ref<boolean>(true);
  const show = ref<boolean>(false);
  const ValidationErrors = ref<ArrayAsociativo>()
  const ImagenesEvidencia = ref<File[]>([])
  const loading = ref<boolean>(false)
  const DetallesGenerales = reactive<DetallesGeneralesBaseProps>({...DetallesGeneralesBase});
  const CondicionesInteriores=ref<CondicionesInterioresForm>(CondicionesInterioresBase)
  const CondicionesExteriores=ref<CondicionesExterioresForm>(CondicionesExterioresBase)
  const Pintura=ref<PinturaForm>(PinturaBase)
  const Inventario=ref<InventarioForm>(InventarioBase)
  const ImageVehiculoEntrada = ref<InstanceType<typeof ZDCanvas> | null>(null);
  const ImageFirmaEntrada = ref<InstanceType<typeof ZDCanvas> | null>(null);
  const ImagenesCargadas=ref<ImagenUpload[]>([])
  const Archivos = reactive<{carro:ImagenUpload|null,firma:ImagenUpload|null}>({carro:null,firma:null})
  const buttonconfirm=computed<buttonconfirmed>(()=>{ 
    return {
      text: DetallesGenerales.id ? 'Guardar Cambios' :'Crear Orden De Servicio',
      classname:'bg-blue-600 text-white',
      onClick:Save,
      disabled:(
        !DetallesGenerales.ubicacion ||
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
        (!DetallesGenerales.id && (ImagenesEvidencia.value.length < 6  || !DetallesGenerales.tipo_id)) ||
        (DetallesGenerales.id && DetallesGenerales.cambiar_archivos && ((ImagenesEvidencia.value.length + ImagenesCargadas.value.length) < 6))  ||
        Object.entries(CondicionesInteriores.value).some(([key,value]) => value == '') ||
        Object.entries(CondicionesExteriores.value).some(([key,value]) => value == '')
      )
    }
  }) 


  const Read= async (id:number) =>{
    loading.value=true;
    try{
      const response=await axios.get(route('Cortana.OrdenServicio.Read'),{params:{id}});
      resetvalues.value=false;
      const generales = response.data.generales;
      if (generales.estimacion) {
        generales.estimacion = new Date(generales.estimacion);
      }
      Object.assign(DetallesGenerales,generales);
      CondicionesInteriores.value=response.data.interiores;
      CondicionesExteriores.value=response.data.exteriores;
      Pintura.value=response.data.pintura;
      Inventario.value=response.data.inventario;
      const urls = response.data.urls;
      loading.value=false;
      Archivos.carro = urls.carro;
      Archivos.firma = urls.firma;
      ImagenesCargadas.value = urls.evidencia;
      await nextTick();
      resetvalues.value=true;
    }catch(error:any){
      if (error.response) {
        MyBasicToast.error(error.response.data.mensaje || "Error Indefinido.");
      }else {
        MyBasicToast.error(error.message || "Error Indefinido.");
      }
      loading.value=false;
    }
  }
  
  const Save = async() =>{
    ImagenesCanvas.value.carro=ImageVehiculoEntrada.value?.GetStrokes();
    ImagenesCanvas.value.firma=ImageFirmaEntrada.value?.GetStrokes();
    ImagenesCanvas.value.carro_img = await ImageCanvas({Canvas:ImageVehiculoEntrada.value,FileName:'carro.jpeg'});
    ImagenesCanvas.value.firma_img = await ImageCanvas({Canvas:ImageFirmaEntrada.value,FileName:'firma.jpeg'});
    const confirm=await ZdAlert({});
    if(!confirm){return}
    loading.value=true

    const Form:OrdenServicioForm={
      id:DetallesGenerales.id,
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
      carro: null,
      firma: null,
      imagenes_evidencia: [],
      condiciones_exteriores: CondicionesExteriores.value,
      condiciones_interiores: CondicionesInteriores.value,
      pintura: Pintura.value,
      inventario: Inventario.value,
      cambiar_archivos:DetallesGenerales.cambiar_archivos
    }

    if(DetallesGenerales.cambiar_archivos){
      Form.carro= ImagenesCanvas.value.carro_img ?? null;
      Form.firma= ImagenesCanvas.value.firma_img ?? null;
      Form.imagenes_evidencia= ImagenesEvidencia.value;
    }

    const response= await CreateorUpdate(Form);
    if(response.status){
      MyBasicToast.success(response.data.message??'Orden De Servicio Creada/Actualizada Con Exito')
      updateVisibility();
    }else if(response.code === 422){
      ValidationErrors.value=response.validationErrors ?? {};
    }else{
      MyBasicToast.error(response.data.message??'Error al crear/actualizar la Orden De Servicio' )
    }
    await new Promise(resolve => setTimeout(resolve, 1000));
    loading.value=false
  }   
  const updateVisibility = () => {
    show.value = false;
    if(OpenModal.value !== null){
      OpenModal.value = null;
    }
  }
  onMounted(async () => {
    optionsgasolima.value = await GetNivelesGasolina();
    optionsequipo.value = await GetStatusPerCategory(11);
    modulosdisponibles.value=await GetModulosDisponibles();
  });
  watch(()=>DetallesGenerales.modulo_orden,()=>{
    if(!resetvalues.value){return}
    DetallesGenerales.vehiculo_concepto_id=null
  })
  watch(()=>DetallesGenerales.modulo_orden,()=>{
    if(!resetvalues.value){return}
    DetallesGenerales.vehiculo_concepto_id=null
  })
  watch(()=>DetallesGenerales.vehiculo,()=>{
    if(!resetvalues.value){return}
    PrintImageTipoVehiculo();
    
  })

  const OpenOtherModal= (val:1)=>{
    ImagenesCanvas.value.carro=ImageVehiculoEntrada.value?.GetStrokes();
    ImagenesCanvas.value.firma=ImageFirmaEntrada.value?.GetStrokes();
    OpenModal.value = val;
  }

  const CloseOtherModal= ()=>{
    OpenModal.value = null;
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
  const PrintImageTipoVehiculo=()=>{
    if(DetallesGenerales.vehiculo){
      GetImageTipoVehiculo({Canvas:ImageVehiculoEntrada.value, id:Number(DetallesGenerales.vehiculo.value)})
    }else{
      ImageVehiculoEntrada.value?.dibujarImagen(null);
    }
    
  }
  const Open = (Id: number | null) => {
    if (Id) {
      Read(Id);
    } else {
      Object.assign(DetallesGenerales, DetallesGeneralesBase);
      CondicionesInteriores.value = CondicionesInterioresBase;
      CondicionesExteriores.value = CondicionesExterioresBase;
      Pintura.value = PinturaBase;
      Inventario.value = InventarioBase;
      ImagenesEvidencia.value = [];
      Archivos.carro = null;
      Archivos.firma = null;
      ImagenesCargadas.value = [];
    }
    nextTick(() => {
      show.value = true;
    });
  };
  defineExpose({
    Open
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
  :show="show && OpenModal === null" 
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
      <InputBasic 
        id="ordenseguimiento" 
        label="Orden De Seguimiento" 
        classname="uppercase"
        type="text" 
        v-model="DetallesGenerales.orden_seguimiento" 
        placeholder="Automatico o Ingresar" 
        :errors="ValidationErrors?.['orden_seguimiento']" 
        :DeleteErrorss="()=>{ValidationErrors?.['orden_seguimiento']}"
      />
      <InputBasic 
        id="ordenopcional" 
        label="Orden Opcional" 
        type="text" 
        v-model="DetallesGenerales.orden_opcional"
        placeholder="Opcional"
        :errors="ValidationErrors?.['orden_opcional']" 
        :DeleteErrorss="()=>{delete ValidationErrors?.['orden_opcional']}"
      />
      <Combobox 
        id="ubicacion" 
        label="Ubicacion"
        classInput="uppercase" 
        v-model="DetallesGenerales.ubicacion" 
        endpoint="Combobox.ubicaciones" 
        placeholder="Buscar o Crear" 
        :errors="ValidationErrors?.['ubicacion']" 
        :DeleteErrors="()=>{delete ValidationErrors?.['ubicacion']}" 
      />
      <template v-if="!DetallesGenerales.id">
        <Select 
          label="Tipo De Presupuesto"  
          v-model="DetallesGenerales.tipo_id" 
          id="presupuestotipo"  
          :options="optionstipos"
          :errors="ValidationErrors?.['tipo_id']" 
          :DeleteErrors="()=>{delete ValidationErrors?.['tipo_id']}" 
        />
        <Select label="Modulo Orden" 
          v-model="DetallesGenerales.modulo_orden" 
          id="modulooreden"
          :canempty="true" 
          :options="modulosdisponibles"
          :errors="ValidationErrors?.['modulo_orden']" 
          :DeleteErrors="()=>{delete ValidationErrors?.['modulo_orden']}" 
        />
        <Select2
          :params="{'id_modulo':DetallesGenerales.modulo_orden}" 
          label="Vehiculo De Los Conceptos" 
          endpoint="Select2.Vehiculos.Conceptos.Modulos" 
          v-model="DetallesGenerales.vehiculo_concepto_id" 
          :empty_message="DetallesGenerales.modulo_orden? 'Sin Resultados':'Selecciona Un Modulo'" 
          placeholder="Buscar Vehiculo" 
          :errors="ValidationErrors?.['vehiculo_concepto_id']" 
          :DeleteErrors="()=>{delete ValidationErrors?.['vehiculo_concepto_id']}" 
        />
      </template>
    </div>
    <Subtitle>Datos Cliente</Subtitle>
    <div class="grid sm:grid-cols-2 gap-2">
      <Select2 
        label="Empresa" 
        id="presupuestoempresa" 
        endpoint="Select2.Empresas" 
        v-model="DetallesGenerales.empresa" 
        placeholder="Buscar Empresas"
        :errors="ValidationErrors?.['empresa']" 
        :DeleteErrors="()=>{delete ValidationErrors?.['empresa']}"
      />
      <Select2   
        label="Cliente" 
        :params="{'empresa_id':DetallesGenerales.empresa?.value}" 
        endpoint="Select2.Clientes" 
        :empty_message="DetallesGenerales.empresa ? 'Sin Resultados' : 'Selecciona una empresa'"
        v-model="DetallesGenerales.cliente" 
        id="presupuestoempresa" 
        placeholder="Buscar Cliente"
        :errors="ValidationErrors?.['cliente']" 
        :DeleteErrors="()=>{delete ValidationErrors?.['cliente']}"
      />
      
    </div>
    <Subtitle>Datos Vehiculo</Subtitle>
     <VehiculoForm
        :Close="()=>emit('close')"
        v-model="DetallesGenerales.vehiculo"
        :errors="ValidationErrors?.['vehiculo']"
        :DeleteErrors="()=>{delete ValidationErrors?.['vehiculo']}"
        :OpenModal="()=>OpenOtherModal(1)" 
      />


    <Subtitle>Datos De Ingreso</Subtitle>
    <div class="grid sm:grid-cols-2 md:grid-cols-4 gap-2" >
      <InputBasic 
        id="telefono" 
        label="Telefono" 
        type="number" 
        v-model="DetallesGenerales.telefono"  
        placeholder="ej. 4433221100"
        :errors="ValidationErrors?.['telefono']" 
        :DeleteErrors="()=>{delete ValidationErrors?.['telefono']}"
      />
      <Datapicker 
        label="Fecha Estimada" 
        v-model="DetallesGenerales.estimacion" 
        :clearable="false" 
        :time="true" 
        :range="false" 
        class="w-full sm:col-span-2 md:col-span-1"
        :errors="ValidationErrors?.['estimacion']" 
        :DeleteErrors="()=>{delete ValidationErrors?.['estimacion']}"
      />
      <InputBasic 
        id="kilometraje" 
        label="Kilometraje" 
        type="number" 
        v-model="DetallesGenerales.kilometraje" 
        placeholder="ej. 392.31"
        :errors="ValidationErrors?.['kilometraje']" 
        :DeleteErrors="()=>{delete ValidationErrors?.['kilometraje']}"
      />
      <Select 
        id="gasolina" 
        :canempty="true" 
        v-model="DetallesGenerales.gasolina" 
        label="Gasolina" 
        :options="optionsgasolima"
        :errors="ValidationErrors?.['gasolina']" 
        :DeleteErrors="()=>{delete ValidationErrors?.['gasolina']}"
      />
    </div>
    <div class="md:col-span-4 sm:col-span-2 flex flex-col md:flex-row gap-2" >
      <ZDCanvas 
        class="md:w-[70%]" 
        strokecolor="red"
        ImageRequired
        :CanEdit="DetallesGenerales.cambiar_archivos"
        :image="Archivos.carro?.url"
        ref="ImageVehiculoEntrada" 
        title="Detalles Del Vehiculo" 
        :DeleteImage="()=>{
          Archivos.carro=null
          PrintImageTipoVehiculo()
        }"
        :errors="ValidationErrors?.['carro']" 
        :DeleteErrorss="()=>{delete ValidationErrors?.['carro']}" 
        />
      <ZDCanvas 
        class="md:w-[30%]" 
        classnamedivcanvas="w-full"
        height="h-[10rem]"
        ref="ImageFirmaEntrada" 
        title="Firma"
        :CanEdit="DetallesGenerales.cambiar_archivos" 
        :image="Archivos.firma?.url"
        :DeleteImage="()=>{
          Archivos.firma=null
          ImageFirmaEntrada?.dibujarImagen(null);
        }"
        :errors="ValidationErrors?.['firma']" 
        :DeleteErrors="()=>{delete ValidationErrors?.['firma']}"
      />
    </div>
    
    <Subtitle>Empleados Encargados</Subtitle>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-2" >
      <Combobox 
        endpoint="Combobox.Administradores_Trasporte" 
        label="Administrador de Trasportes" 
        id="administradortrasporte" 
        v-model="DetallesGenerales.administrador" 
        placeholder="Buscar o Crear "
        :errors="ValidationErrors?.['administrador']" 
        :DeleteErrors="()=>{delete ValidationErrors?.['administrador']}"
      />
      <Combobox 
        endpoint="Combobox.Jefes_Procesos" 
        label="Jefe De Procesos" 
        id="jefeproceso" 
        v-model="DetallesGenerales.jefe"  
        placeholder="Buscar o Crear"
        :errors="ValidationErrors?.['jefe']" 
        :DeleteErrors="()=>{delete ValidationErrors?.['jefe']}"
      />
      <Combobox 
        endpoint="Combobox.Trabajadores" 
        label="Trabajador" 
        id="trabajador" 
        v-model="DetallesGenerales.trabajador"  
        placeholder="Buscar o Crear"
        :errors="ValidationErrors?.['trabajador']" 
        :DeleteErrors="()=>{delete ValidationErrors?.['trabajador']}"
      />
      <Combobox 
        endpoint="Combobox.Tecnicos" 
        label="Tecnico" 
        id="tecnico" 
        v-model="DetallesGenerales.tecnico"  
        placeholder="Buscar o Crear"
        :errors="ValidationErrors?.['tecnico']" 
        :DeleteErrors="()=>{delete ValidationErrors?.['tecnico']}"
      />
    </div>
    <Subtitle>Notas</Subtitle>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-2" >
      <Textarea id="indicacionescliente" label="Indicaciones Del Cliente" 
        v-model="DetallesGenerales.indicaciones_cliente" 
        placeholder="Escribe las indicaciones del cliente aqui..." 
        classname="h-24"
        :errors="ValidationErrors?.['indicaciones_cliente']" 
        :DeleteErrors="()=>{delete ValidationErrors?.['indicaciones_cliente']}"
      />
      <Textarea id="notasmecanico" label="Descripcion Mano De Obra" 
        v-model="DetallesGenerales.descripcion_mo" 
        placeholder="Escribe las notas del mecanico aqui..." 
        classname="h-24"
        :errors="ValidationErrors?.['descripcion_mo']" 
        :DeleteErrors="()=>{delete ValidationErrors?.['descripcion_mo']}"
      />
    </div>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-2 mt-2">
      <InventarioTemplate v-model="Inventario"/>
      <PinturaTemplate v-model="Pintura"/>
    </div>
    <ValuesCondicionesEquipo/>
    <CondicionesInterioresTemplate 
      v-model="CondicionesInteriores" 
      :DeleteErrorss="(key:string)=>{delete ValidationErrors?.[key]}" 
      :errors="Object.entries(ValidationErrors??{}).filter(([key]) => key.includes('condiciones_interiores')).map((item)=>{return{Key:item[0],errors:item[1]}})"/>
    <CondicionesExterioresTemplate 
      v-model="CondicionesExteriores"
      :DeleteErrorss="(key:string)=>{delete ValidationErrors?.[key]}" 
      :errors="Object.entries(ValidationErrors??{}).filter(([key]) => key.includes('condiciones_exteriores')).map((item)=>{return{Key:item[0],errors:item[1]}})"/>
    <ImagenesEvidencias 
      v-model:Imagenes="ImagenesEvidencia" 
      v-model:ImagenesUpload="ImagenesCargadas" 
      :CanEditImages="DetallesGenerales.cambiar_archivos"
      :DeleteErrors="(key:string)=>{delete ValidationErrors?.[key]}" 
      :errors="Object.entries(ValidationErrors??{}).filter(([key]) => key.includes('imagenes_evidencia')).map((item)=>{return{Key:item[0],errors:item[1]}})"
    />
  </template>
 
  </BaseModal>
</template>