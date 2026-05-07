<script setup lang="ts">
  import Subtitle from '@/components/Zcrat/Elements/Subtitle.vue'
  import Checkbox from '@/components/Zcrat/Inputs/form/Checkbox.vue'
  import { PinturaForm } from '@/types/OrdenServicio'
  import { PinturaInputs } from '@/utils/variables/ordenservicio'
  const Pintura = defineModel<PinturaForm>({ required:true })
</script>

<template>
  <div class="border-2 rounded-md p-2">
    <subtitle>Condiciones Pintura</Subtitle>

    <div class="grid sm:grid-cols-2 2xl:grid-cols-3">
      <Checkbox
        v-for="(item,index) in PinturaInputs"
        :key="'inventario-'+index"
        value="1"
        :checked="Pintura[index] === true"
        :label="item"
        @update:checked="()=>{
          Pintura[index] = Pintura[index] === true ? false : true
        }"
      />
    </div>
  </div>
</template>