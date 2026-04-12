<script setup lang="ts">
  import Subtitle from '@/components/Zcrat/Elements/Subtitle.vue';
  import Checkbox from '@/components/Zcrat/Inputs/form/Checkbox.vue';
  import { InventarioForm } from '@/types/OrdenServicio';
  import { InventarioBase, InventarioInputs } from '@/utils/variables/ordenservicio';
  import { reactive, watch } from 'vue';
  const Inventario=reactive<InventarioForm>({...InventarioBase})
  const ModelValue = defineModel<InventarioForm>()
  watch(Inventario,(val) => {
    ModelValue.value = { ...val }
  },{ deep: true })

  watch(() => ModelValue.value,(val) => {
    Object.assign(Inventario, val)
  },{ deep: true })
</script>
<template>
  <div class="border-2 rounded-md p-2">
    <subtitle>Inventario Equipo</Subtitle>
    <div class="grid sm:grid-cols-2 2xl:grid-cols-3">
      <Checkbox v-for="(item,index) 
      in InventarioInputs" 
      value='1' 
      :key="'inventario-'+index" 
      :checked="Inventario[index] ==='1'" 
      :label="item" 
      @update:checked="()=>{Inventario[index] = Inventario[index] === '1' ? '0' : '1'}"/>
    </div>
  </div>
</template>