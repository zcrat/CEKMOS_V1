<script setup lang="ts">
  import Subtitle from '@/components/Zcrat/Elements/Subtitle.vue';
  import Checkbox from '@/components/Zcrat/Inputs/form/Checkbox.vue';
  import { PinturaForm } from '@/types/OrdenServicio';
  import { PinturaBase, PinturaInputs } from '@/utils/variables/ordenservicio';
  import { reactive, watch } from 'vue';
  const Pintura=reactive<PinturaForm>(PinturaBase)
  const ModelValue = defineModel<PinturaForm>()
  watch(Pintura,(val) => {
    ModelValue.value = { ...val }
  },{ deep: true })

  watch(() => ModelValue.value,(val) => {
    Object.assign(Pintura, val)
  },{ deep: true })
</script>
<template>
  <div class="border-2 rounded-md p-2">
    <subtitle>Condiciones Pintura</Subtitle>
    <div class="grid sm:grid-cols-2 2xl:grid-cols-3">
      <Checkbox v-for="(item,index) in PinturaInputs" 
        value='1' 
        :key="'inventario-'+index" :checked="Pintura[index] ==='1'" :label="item" @update:checked="()=>{Pintura[index] = Pintura[index] === '1' ? '0' : '1'}"/>
    </div>
  </div>
</template>