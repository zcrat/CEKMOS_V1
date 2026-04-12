<script setup lang="ts">
  import OptionsCondicionesEquipo from '@/components/Zcrat/Elements/OptionsCondicionesEquipo.vue';
  import OptionsCondicionesEquipo2 from '@/components/Zcrat/Elements/OptionsCondicionesEquipo2.vue';
  import Subtitle from '@/components/Zcrat/Elements/Subtitle.vue';
  import { option } from '@/types/generales';
  import { CondicionesInterioresForm } from '@/types/OrdenServicio';
  import GetStatusPerCategory from '@/utils/functions/select/StatusPerCategory';
  import { CondicionesInterioresBase, CondicionesInterioresInputs } from '@/utils/variables/ordenservicio';
  import { onMounted, reactive, ref, watch } from 'vue';

  const ModelValue=defineModel<CondicionesInterioresForm>()
  const CondicionesInteriores=reactive<CondicionesInterioresForm>(
    {...CondicionesInterioresBase})
  
  const optionsequipo=ref<option[]>([])
  const CondicionesInterioresAll1=ref<string|null>(null);
  const CondicionesInterioresAll2=ref<string|null>(null);
  const CondicionesInterioresAll3=ref<string|null>(null);
  onMounted(async () => {
    optionsequipo.value = await GetStatusPerCategory(11);
  });
  watch(CondicionesInterioresAll1,()=>{
    const keys = Object.keys(
      CondicionesInterioresInputs['PANALES DE PUERTA']
    ) as Array<keyof typeof CondicionesInterioresInputs['PANALES DE PUERTA']>;

    keys.forEach(k => {
      if(CondicionesInterioresAll1.value !== null){
        console.log(CondicionesInterioresAll1.value)
        CondicionesInteriores[k] = CondicionesInterioresAll1.value ?? '';
      }
    });
  })
  watch(
    () => {
      const keys = Object.keys(
        CondicionesInterioresInputs['PANALES DE PUERTA']
      ) as Array<keyof typeof CondicionesInterioresInputs['PANALES DE PUERTA']>;

      return keys.map(k => CondicionesInteriores[k]);
    },
    (values) => {
      if (values.length === 0) return;
      const first = values[0];
      const allEqual = values.every(v => v === first);
      if (allEqual) {
        CondicionesInterioresAll1.value = first ?? '';
      }else{
        console.log('se puso null')
        CondicionesInterioresAll1.value = null;
      }
    }
  );
  watch(CondicionesInterioresAll2,()=>{
    const keys = Object.keys(
      CondicionesInterioresInputs['ASIENTOS']
    ) as Array<keyof typeof CondicionesInterioresInputs['ASIENTOS']>;

    keys.forEach(k => {
      if(CondicionesInterioresAll2.value !== null){
        CondicionesInteriores[k] = CondicionesInterioresAll2.value ?? '';
      }
    });
  })
  watch(
    () => {
      const keys = Object.keys(
        CondicionesInterioresInputs['ASIENTOS']
      ) as Array<keyof typeof CondicionesInterioresInputs['ASIENTOS']>;
      return keys.map(k => CondicionesInteriores[k]);
    },
    (values) => {
      if (values.length === 0) return;
      const first = values[0];
      const allEqual = values.every(v => v === first);
      if (allEqual) {
        CondicionesInterioresAll2.value = first ?? '';
      }else{
        console.log('se puso null')
        CondicionesInterioresAll2.value = null;
      }
    }
  );
  watch(CondicionesInterioresAll3,()=>{
    const keys = Object.keys(
      CondicionesInterioresInputs['OTROS']
    ) as Array<keyof typeof CondicionesInterioresInputs['OTROS']>;

    keys.forEach(k => {
      if(CondicionesInterioresAll3.value !== null){
        CondicionesInteriores[k] = CondicionesInterioresAll3.value ?? '';
      }
    });
  })
  watch(
    () => {
      const keys = Object.keys(
        CondicionesInterioresInputs['OTROS']
      ) as Array<keyof typeof CondicionesInterioresInputs['OTROS']>;
      return keys.map(k => CondicionesInteriores[k]);
    },
    (values) => {
      if (values.length === 0) return;
      const first = values[0];
      const allEqual = values.every(v => v === first);
      if (allEqual) {
        CondicionesInterioresAll3.value = first ?? '';
      }else{
        console.log('se puso null')
        CondicionesInterioresAll3.value = null;
      }
    }
  );
  watch(CondicionesInteriores,(val) => {
    ModelValue.value = { ...val }
  },{ deep: true })

  watch(() => ModelValue.value,(val) => {
    console.log
    Object.assign(CondicionesInteriores, val)
  },{ deep: true })
</script>
<template>
    <div class="border-2 rounded-md p-2">
      <Subtitle>
        Condiciones Equipo Interior
      </Subtitle>
      <div class="grid sm:grid-cols-2 gap-2">
        <div >
          <Subtitle bg="bg-[--color4]">
            <div class="flex items-center justify-center gap-2 w-full">
          Puertas
          <OptionsCondicionesEquipo
            v-model="CondicionesInterioresAll1" 
            :options="optionsequipo"
            :toggle="true"
          >
          </OptionsCondicionesEquipo>
        </div></Subtitle>
          <div class=" justify-center items-center grid lg:grid-cols-2 2xl:grid-cols-4 gap-2">
            
            <OptionsCondicionesEquipo v-for="(item,key) in CondicionesInterioresInputs['PANALES DE PUERTA']" 
            :label="item" 
            :key="'inventario-'+key" 
            v-model="CondicionesInteriores[key]" 
            :options="optionsequipo">
          </OptionsCondicionesEquipo>
          </div>
        </div>
        <div>
          <Subtitle bg="bg-[--color4]"><div class="flex items-center justify-center gap-2 w-full">
          Asientos
          <OptionsCondicionesEquipo
            v-model="CondicionesInterioresAll2" 
            :options="optionsequipo"
            :toggle="true"
          >
          </OptionsCondicionesEquipo>
        </div></Subtitle>
          <div class="flex flex-col items-center lg:grid lg:grid-cols-2 2xl:grid-cols-4 gap-2">
            <OptionsCondicionesEquipo v-for="(item,key) in CondicionesInterioresInputs['ASIENTOS']" 
            :label="item" 
            :key="'inventario-'+key" 
            v-model="CondicionesInteriores[key]" 
            :options="optionsequipo">
          </OptionsCondicionesEquipo>
          </div>
        </div>
      </div>
      <div>
        <Subtitle bg="bg-[--color4]"><div class="flex items-center justify-center gap-2 w-full">
          Otros
          <OptionsCondicionesEquipo
            v-model="CondicionesInterioresAll3" 
            :options="optionsequipo"
            :toggle="true"
          >
          </OptionsCondicionesEquipo>
        </div></Subtitle>
        <div class="grid sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 2xl:grid-cols-6 gap-2">
            <OptionsCondicionesEquipo v-for="(item,key) in CondicionesInterioresInputs['OTROS']" 
            :label="item" 
            :key="'inventario-'+key" 
            v-model="CondicionesInteriores[key]" 
            :options="optionsequipo">
          </OptionsCondicionesEquipo>
        </div>
      </div>
    </div>
</template>