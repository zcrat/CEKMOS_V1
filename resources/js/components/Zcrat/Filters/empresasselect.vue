<script setup lang="ts">
import ZDSelect from '@/components/Zcrat/Elements/ZDSelect.vue'
const empresa =defineModel<string| null>({ default: null })
const props = withDefaults(defineProps<{
  canNew?:boolean
}>(), {
  canNew:true,
})
</script>
<template>
    <div class="min-w-[10rem] w-auto">
        <ZDSelect endpoint="Select2.Empresas" :can-new="props.canNew" type="empresas" v-model="empresa" id="empresasfilter" placeholder="Buscar Empresas"/>
    </div> 
</template>