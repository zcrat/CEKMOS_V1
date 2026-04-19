<script setup lang="ts">
import ProgresBar from '@/components/Zcrat/Elements/ProgresBar.vue';


const props = defineProps<{
  porcentage?: number;
  text:string;
}>();
</script>

<template>
    <div id="loadingdata" class="flex items-center w-full h-full min-h-[11rem] min-w-[15rem]">
        <div class="flex flex-col items-center m-2 w-full gap-4">
            <h3 className="text-[1.5rem] font-bold text-center">{{ props.text }}</h3>
            <ProgresBar v-if="props.porcentage"  :width="props.porcentage"/>
            <div v-else className="spinnerp"></div>
        </div>
    </div>
</template>