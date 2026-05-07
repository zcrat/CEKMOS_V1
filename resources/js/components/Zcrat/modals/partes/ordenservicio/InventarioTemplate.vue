<script setup lang="ts">
  import Subtitle from '@/components/Zcrat/Elements/Subtitle.vue';
  import Checkbox from '@/components/Zcrat/Inputs/form/Checkbox.vue';
  import { InventarioForm } from '@/types/OrdenServicio';
  import { InventarioInputs } from '@/utils/variables/ordenservicio';
  const Inventario=defineModel<InventarioForm>({ required:true })
</script>
<template>
  <div class="border-2 rounded-md p-2">
    <subtitle>Inventario Equipo</Subtitle>
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