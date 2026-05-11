<script setup lang="ts">
  import Subtitle from '@/components/Zcrat/Elements/Subtitle.vue';
  import Checkbox from '@/components/Zcrat/Inputs/form/Checkbox.vue';
  import { InventarioForm } from '@/types/OrdenServicio';
  import { InventarioInputs } from '@/utils/variables/ordenservicio';
  import { ref } from 'vue';

  const Inventario=defineModel<InventarioForm>({ required:true })

  const UpdateAll=(val:boolean)=>{
    Inventario.value.llanta=val;
    Inventario.value.cables=val;
    Inventario.value.estuche=val;
    Inventario.value.llave_tuerca=val;
    Inventario.value.triangulo=val;
    Inventario.value.tarjeta_circulacion=val;
    Inventario.value.cubreruedas=val;
    Inventario.value.candado_rueda=val;
    Inventario.value.extinguidor=val;
    Inventario.value.gato=val;
    Inventario.value.placas=val;
  }
</script>
<template>
  <div class="border-2 rounded-md p-2">
    <subtitle><div class="flex items-center justify-center">Inventario Equipo  <Checkbox 
      value='1' 
      :key="'inventarioall'" 
      :checked="Object.entries(Inventario).every(([_,value]) => value )" 
      @update:checked="()=>{UpdateAll(!Object.entries(Inventario).every(([_,value]) => value))}"/></div></subtitle>
    <div class="grid sm:grid-cols-2 2xl:grid-cols-3">
      <Checkbox v-for="(item,index) 
      in InventarioInputs" 
      value='1' 
      :key="'inventario-'+index" 
      :checked="Inventario[index] === true" 
      :label="item" 
      @update:checked="()=>{Inventario[index] = Inventario[index] === true ? false : true}"/>
    </div>
  </div>
</template>