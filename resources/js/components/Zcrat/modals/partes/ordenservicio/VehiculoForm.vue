<script setup lang="ts">
    import InputBasic from '@/components/Zcrat/Inputs/form/InputBasic.vue'
    import { ref, watch ,reactive} from 'vue' 
    import {ArrayAsociativo, type option} from '@/types/generales'
    import TiposVehiculos from '@/components/Zcrat/Forms/TiposVehiculos.vue';
    import {EconomicoForm} from '@/types/OrdenServicio'
    import {EconomicoBase} from '@/utils/variables/ordenservicio'
    import {type Vehiculo as VehiculoProps} from '@/types/generales'
    import axios from 'axios';
    import Select2 from '@/components/Zcrat/Elements/Select2.vue';
import Button from '@/components/Zcrat/Inputs/Button.vue';
    const props = defineProps<{
        GetImage?: (val:number)=>void
        Close?: ()=>void
        errors?: string[]
        DeleteErrors?: ()=>void
        OpenModal?:()=>void 
        ShowButtons?:boolean      
    }>()
    const Vehiculo = reactive<EconomicoForm>(EconomicoBase)
    const VehiculoId=defineModel<option|null>()
    const ValidationErrors = ref<ArrayAsociativo>()
    watch(()=>Vehiculo.tipo_id,(val)=>{
        if(val){props.GetImage?.(val)}
    })
    watch(VehiculoId,()=>{
        if(VehiculoId.value){
            Read(VehiculoId.value.value);
        }else{
            Vehiculo.placas='';
            Vehiculo.economico='';
            Vehiculo.vin='';
            Vehiculo.anio='';
            Vehiculo.tipo_id=null;
            Vehiculo.color='';
            Vehiculo.modelo='';
            Vehiculo.marca='';
            Vehiculo.id=undefined;
        }
    })
    const Read = async (id:string|number) => {
        try {
        const response = await axios.get(route('Vehiculo.Find'),{params:{id} })
        const data:VehiculoProps=response.data.vehiculo;
            Vehiculo.id=data.id;
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
            props.Close?.()
        }
    }
    
</script>
<template >
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-2 mt-2">
    <Select2 
        label="Vehiculo" 
        :buttonNew="OpenModal"
        id="Vehiculo" 
        endpoint="Select2.Economico" 
        v-model="VehiculoId" 
        placeholder="Buscar por Economico o Placas"
        :errors="errors"
        :DeleteErrors="DeleteErrors"
    />
    <template v-if="VehiculoId">
    <InputBasic 
        id="Vin" 
        disabled 
        label="Vin" 
        type="text" 
        v-model="Vehiculo.vin" 
        placeholder="Ej.JJSOE18P388988750 "
        :errors="ValidationErrors?.['vin']" 
        :DeleteErrors="()=>{delete ValidationErrors?.['vin']}"
        />
        <InputBasic 
        id="Año" 
        disabled 
        label="Año" 
        type="number" 
        v-model="Vehiculo.anio"  
        placeholder="ej. 2024"
        :errors="ValidationErrors?.['anio']" 
        :DeleteErrors="()=>{delete ValidationErrors?.['anio']}"
        />
        <InputBasic id="Marca" 
        disabled 
        label="Marcas"
        type="text" 
        v-model="Vehiculo.marca" 
        classname="uppercase"
        placeholder="ej. AUDI"
        :errors="ValidationErrors?.['marca']" 
        :DeleteErrors="()=>{delete ValidationErrors?.['marca']}"
        />
        <InputBasic 
        id="Modelo" 
        disabled 
        label="Modelo" 
        type="text" 
        v-model="Vehiculo.modelo" 
        classname="uppercase" 
        placeholder="ej. A3"
        :errors="ValidationErrors?.['modelo']" 
        :DeleteErrors="()=>{delete ValidationErrors?.['modelo']}"
        />
        <InputBasic 
        id="Color" 
        disabled 
        label="Color" 
        type="text" 
        v-model="Vehiculo.color" 
        classname="uppercase" 
        placeholder="ej. Rojo"
        :errors="ValidationErrors?.['modelo']" 
        :DeleteErrors="()=>{delete ValidationErrors?.['modelo']}"
        />
        <TiposVehiculos 
        disabled 
        label="Tipo De Vehiculo" 
        id="tipovehiculo" 
        v-model="Vehiculo.tipo_id"
        :errors="ValidationErrors?.['modelo']" 
        :DeleteErrors="()=>{delete ValidationErrors?.['modelo']}"
        />
        </template>
    </div>
    <div class="flex gap-2 mt-2" v-if="ShowButtons">
        <Button text="Guardar" />
        <Button text="Cancelar" type="delete"/>
    </div>
</template>