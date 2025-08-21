<script setup lang="ts">
import ProgresBar from '@/components/Zcrat/Elements/ProgresBar.vue';


const props = defineProps<{
  porcentage?: number;
  text:string;
}>();
console.log('refrescada');
</script>

<template>
    <div id="loadingdata" className="flex flex-col justify-center items-center min-w-full">
        <h3 className="m-[3rem] text-[1.5rem] font-bold">{{ props.text }}</h3>
        <ProgresBar v-if="props.porcentage"  :width="props.porcentage"/>
        <div v-else className="spinnerp"></div>
    </div>
</template>