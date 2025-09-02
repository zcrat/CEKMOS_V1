<script setup lang="ts">
  import Checkbox from '@/components/Zcrat/Inputs/form/Checkbox.vue'
  import Poper from '@/components/Zcrat/Elements/poper.vue' 
  import { computed } from 'vue';
import { ref } from 'vue';
import type {ComponentInterface} from '@/utils/interfaces/tablecomponent'
interface ListadoModulos{
    id:number,
    label:string
}
const props = defineProps<{
    Listado:ListadoModulos[]
}>()
const ListadoModulos=computed(() => props.Listado.map(function(fila){
    return{
        element: Checkbox,
        props: {
            id:'pedientes',
            checked: true,
            name:'pedientes',
            label: fila.label,
            value: fila.id,
            classname: 'rounded-lg border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500',
        }
    }}));
</script>

<template>
     <poper :father="'Modulos'" :classname="''"
                    :stoppropagation="true"
                    :align="'left'"
                    :children="ListadoModulos"
                />
</template>