<script setup lang="ts">
import ProgressBar from '@/components/Zcrat/Elements/ProgressBar.vue';


const props = defineProps<{
  percentage?: number;
  text:string;
}>();
</script>

<template>
    <div id="loadingdata" class="flex items-center w-full h-full min-h-[11rem] min-w-[15rem]">
        <div class="flex flex-col items-center m-2 w-full gap-4">
            <h3 class="text-center text-[1.5rem] font-bold">{{ props.text }}</h3>
            <ProgressBar
              v-if="props.percentage !== undefined"
              :width="props.percentage"
            />
            <div v-else class="spinnerp"></div>
        </div>
    </div>
</template>
