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
  import ZDCanvas from '../Elements/ZDCanvas.vue'
  import TiposVehiculos from '../Forms/TiposVehiculos.vue';
  import GetStatusPerCategory from '@/utils/functions/select/StatusPerCategory'
  import {EconomicoForm,OrdenServicioForm,ImagenesForm,CondicionesInterioresForm,CondicionesExterioresForm, PinturaForm, InventarioForm} from '@/types/OrdenServicio'
  import {optionstipos} from '@/utils/variables/options'
  import {DetallesGeneralesBase,CondicionesInterioresBase,CondicionesExterioresBase,EconomicoDefault, PinturaBase, InventarioBase} from '@/utils/variables/ordenservicio'
  import {GetDataVehiculoEconomico, GetDataVehiculoPlacas, GetImageTipoVehiculo, SaveCarAndFirma } from '@/utils/functions/ordenservicio';
  import ImagenesEvidencias from './partes/ordenservicio/ImagenesEvidencias.vue';
  import CondicionesInterioresTemplate from './partes/ordenservicio/CondicionesInteriores.vue';
  import CondicionesExterioresTemplate from './partes/ordenservicio/CondicionesExteriores.vue';
  import ValuesCondicionesEquipo from './partes/ordenservicio/ValuesCondicionesEquipo.vue';
  import InventarioTemplate from './partes/ordenservicio/InventarioTemplate.vue';
  import PinturaTemplate from './partes/ordenservicio/PinturaTemplate.vue';
  const emit = defineEmits(['close'])
  const props = defineProps<{show: boolean}>()
  const optionsgasolima=ref<option[]>([])
  const optionsequipo=ref<option[]>([])
  const modulosdisponibles=ref<option[]>([])
  const CanEditImages = ref<boolean>(true)
  const updateVisibility = () => {
    emit('close')
  }
  onMounted(async () => {console.log('montado')
    optionsgasolima.value = await GetNivelesGasolina();
    optionsequipo.value = await GetStatusPerCategory(11);
    modulosdisponibles.value=await GetModulosDisponibles();
  });
  const Economico = reactive<EconomicoForm>(EconomicoDefault)
  const Imagenes = ref<ImagenesForm[]>([])
  const DetallesGenerales = reactive<OrdenServicioForm>(DetallesGeneralesBase);
  const CondicionesInteriores=ref<CondicionesInterioresForm>(CondicionesInterioresBase)
  const CondicionesExteriores=ref<CondicionesExterioresForm>(CondicionesExterioresBase)
  const Pintura=ref<PinturaForm>({...PinturaBase})
  const Inventario=ref<InventarioForm>({...InventarioBase})
  const KeysOptional=['orden_opcional','orden_seguimiento','id'];
  const ImageVehiculoEntrada = ref<InstanceType<typeof ZDCanvas> | null>(null);
  const ImageFirmaEntrada = ref<InstanceType<typeof ZDCanvas> | null>(null);
  const buttonconfirm=computed<buttonconfirmed>(()=>{ 
    return {
      text:'Crear Presupuesto',
      classname:'bg-blue-600 text-white',
      onClick:async ()=>{
        await SaveCarAndFirma({Carro:ImageVehiculoEntrada.value,Firma:ImageFirmaEntrada.value,Imagenes:Imagenes.value})
      },
      disabled:Object.entries(DetallesGenerales)
      .filter(([key]) => !KeysOptional.includes(key))
      .some(([_,value]) => value === null || value === '') 
      ||
      Object.entries(Economico)
      .filter(([key]) => !['id'].includes(key))
      .some(([_,value]) => value === null || value === '')
      ||
      Object.entries(CondicionesExteriores.value)
      .filter(([key]) => !['id','DetallesGeneralesId'].includes(key))
      .some(([_,value]) => value === null || value === '')
      ||
      Object.entries(CondicionesInteriores.value)
      .filter(([key]) => !['id','DetallesGeneralesId'].includes(key))
      .some(([_,value]) => value === null || value === '')
      ||
      Imagenes.value.filter(item=>item.tipo_id ===3).length < 6
    }

  })  
  watch(()=>Economico.tipo_id,(val)=>{
    if(val){GetImageTipoVehiculo({Canvas:ImageVehiculoEntrada.value, Tipo:val})}
  })
  watch(()=>DetallesGenerales.modulo_orden,()=>{
    DetallesGenerales.vehiculo_concepto_id=null
  })
  watch(()=>DetallesGenerales.empresa,()=>{
    DetallesGenerales.cliente=null
  })
  watch(Inventario,(val)=>{
    console.log(Inventario.value)
  })
</script>
<template>
  <BaseModal modaltitle="Nueva Orden De Servicio" 
  :position="'center'" 
  :show="props.show" 
  @close="updateVisibility" 
  :buttonconfirm="buttonconfirm" >
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
          :OnBlur="()=>{GetDataVehiculoEconomico(Economico)}"
          placeholder="Buscar o Crear Economico" 
          :timeout="400"
          />
        <Combobox 
        endpoint="Combobox.Vehiculo.Placas" 
        label="Placas" 
        id="placas" 
        v-model="Economico.placas"
        :OnBlur="()=>{GetDataVehiculoPlacas(Economico)}"
        placeholder="Buscar o Crear PLacas"
        :timeout="400"
         />
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
      <InventarioTemplate v-model="Inventario"/>
      <PinturaTemplate v-model="Pintura"/>
    </div>
    <ValuesCondicionesEquipo/>
    <CondicionesInterioresTemplate v-model="CondicionesInteriores"/>
    <CondicionesExterioresTemplate v-model="CondicionesExteriores"/>
    <ImagenesEvidencias v-model="Imagenes" :CanEditImages="CanEditImages"/>
  </BaseModal>
</template>