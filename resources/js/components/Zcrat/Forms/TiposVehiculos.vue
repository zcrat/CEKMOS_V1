<script setup lang="ts">
    import Select from '../Elements/Select.vue';
    import { ref,onMounted} from 'vue' ;
    import {type option} from '@/types/generales';
    import axios from 'axios' 

    defineProps<{
        disabled?:boolean
    }>()
    const tiposvehiculos=ref<option[]>([]);
    const SelectValue=defineModel<number | null>();

    onMounted(async () => {
        try {
            const response = await axios.get(route('select.tipos.vehiculos'))
            tiposvehiculos.value=response.data.options.map((item:option)=>{return {
                value:Number(item.value),
                label:item.label,
            }});
        } catch (error: any) {
            tiposvehiculos.value=[]
        } 
    });

</script>
<template>
     <Select label="Tipo De Vehiculo" id="tipovehiculo" :options="tiposvehiculos" v-model="SelectValue" :disabled="disabled"></Select>
</template>