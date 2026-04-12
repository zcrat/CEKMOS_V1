<script setup lang="ts">
import OptionsCondicionesEquipo from '@/components/Zcrat/Elements/OptionsCondicionesEquipo.vue';
import Subtitle from '@/components/Zcrat/Elements/Subtitle.vue';
import { option } from '@/types/generales';
import { CondicionesExterioresForm } from '@/types/OrdenServicio';
import GetStatusPerCategory from '@/utils/functions/select/StatusPerCategory';
import { CondicionesExterioresBase, CondicionesExterioresInputs } from '@/utils/variables/ordenservicio';
import { onMounted, reactive, ref, watch } from 'vue';

const ModelValue = defineModel<CondicionesExterioresForm>()
const CondicionesExteriores=reactive<CondicionesExterioresForm>({...CondicionesExterioresBase})
watch(CondicionesExteriores,(val) => {
  ModelValue.value = { ...val }
},{ deep: true })

watch(() => ModelValue.value,(val) => {
  Object.assign(CondicionesExteriores, val)
},{ deep: true })
const optionsequipo=ref<option[]>([])
const CondicionesExterioresAll=ref<string|null>(null);

onMounted(async () => {
  optionsequipo.value = await GetStatusPerCategory(11);
});

watch(CondicionesExterioresAll,()=>{
  const keys = Object.keys(
    CondicionesExterioresInputs
  ) as Array<keyof typeof CondicionesExterioresInputs>;

  keys.forEach(k => {
    if(CondicionesExterioresAll.value !== null){
      CondicionesExteriores[k] = CondicionesExterioresAll.value ?? '';
    }
  });
})
watch(
  () => {
    const keys = Object.keys(
      CondicionesExterioresInputs
    ) as Array<keyof typeof CondicionesExterioresInputs>;
    return keys.map(k => CondicionesExteriores[k]);
  },
  (values) => {
    if (values.length === 0) return;
    const first = values[0];
    const allEqual = values.every(v => v === first);
    if (allEqual) {
      CondicionesExterioresAll.value = first ?? '';
    }else{
      console.log('se puso null')
      CondicionesExterioresAll.value = null;
    }
  }
);
</script>
<template>
  <div class="border-2 rounded-md p-2 mt-2">
    <Subtitle><div class="flex items-center justify-center gap-2 w-full">
        Condiciones Equipo Exterior
        <OptionsCondicionesEquipo
          v-model="CondicionesExterioresAll" 
          :options="optionsequipo"
          :toggle="true"
          invertClases
        >
        </OptionsCondicionesEquipo>
      </div></Subtitle>
    <div class="grid gap-2 sm:grid-cols-2  md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 ">
      <OptionsCondicionesEquipo  :key="'equipo-exterior-'+index"  v-for="(item,index) in CondicionesExterioresInputs" :label="item" v-model="CondicionesExteriores[index]"/>
    </div>
  </div>
</template>