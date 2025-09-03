<script setup lang="ts">
import { ref} from 'vue'
import ZDSelect from '@/components/Zcrat/Elements/ZDSelect.vue'
interface option {
    value: number | string
    label: string
}
const empresa =ref<option | null>(null)
</script>
<template>
    <div class="min-w-[10rem] w-auto">
        <ZDSelect endpoint="Select2.Empresas" type="empresas" v-model="empresa" id="empresasfilter"/>
    </div> 
</template>