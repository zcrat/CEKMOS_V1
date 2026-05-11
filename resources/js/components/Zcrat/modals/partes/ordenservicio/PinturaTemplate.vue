<script setup lang="ts">
  import Subtitle from '@/components/Zcrat/Elements/Subtitle.vue'
  import Checkbox from '@/components/Zcrat/Inputs/form/Checkbox.vue'
  import { PinturaForm } from '@/types/OrdenServicio'
  import { PinturaInputs } from '@/utils/variables/ordenservicio'
  const Pintura = defineModel<PinturaForm>({ required:true })
  const UpdateAll=(val:boolean)=>{
    Pintura.value.decolorada=val;
    Pintura.value.color_desigual=val;
    Pintura.value.rayones=val;
    Pintura.value.grietas=val;
    Pintura.value.golpes=val;
    Pintura.value.emblemas=val;
    Pintura.value.logos=val;
    Pintura.value.rociado=val;
    Pintura.value.granizo=val;
    Pintura.value.lluvia=val;
  }
</script>

<template>
  <div class="border-2 rounded-md p-2">
    <subtitle><div class="flex items-center justify-center">Condiciones Pintura  <Checkbox 
      value='1' 
      :key="'inventarioall'" 
      :checked="Object.entries(Pintura).every(([_,value]) => value )" 
      @update:checked="()=>{UpdateAll(!Object.entries(Pintura).every(([_,value]) => value))}"/></div></subtitle>

    <div class="grid sm:grid-cols-2 2xl:grid-cols-3">
      <Checkbox
        v-for="(item,index) in PinturaInputs"
        :key="'Pintura-'+index"
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