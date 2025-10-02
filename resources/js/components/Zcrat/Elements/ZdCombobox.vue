<script setup lang="ts">
import { ref,defineModel, watch } from 'vue'
import InputBasic from '../Inputs/form/InputBasic.vue'
import axios from 'axios'

const search = defineModel<string|null>()
const isactive = ref<boolean>(false);
const posibleitems = ref<string[]>([])
const inputRef = ref<HTMLElement | null>(null)
const props = withDefaults(defineProps<{
  id: string
  endpoint: string
  placeholder?: string
  label?: string
  timeout?: number
}>(), {
  timeout: 500,
  placeholder: 'Buscar...'
})

const loading = ref(false)

const GetOptions = async () => {
  try {
    loading.value = true
    const response = await axios.get(route(props.endpoint), {
      params: { search: search.value }
    })
    posibleitems.value = response.data.options
  } catch (error) {
    console.error(error)
  } finally {
    loading.value = false
  }
}

let timeout: ReturnType<typeof setTimeout>
const changesearch = (option:string) => {
  search.value=option;
  inputRef.value?.blur()
}
watch(search, () => {
  clearTimeout(timeout);
  timeout = setTimeout(() => { GetOptions() }, props.timeout);
})
</script>

<template>
    
    <div class="relative">
      <InputBasic :id="props.id" ref="inputRef" :label="props.label" type="text" v-model="search" :OnFocus="()=>isactive=true" :OnBlur="()=>isactive=false"  :placeholder="props.placeholder"/>
      <div v-if="posibleitems.length > 0 && isactive" class="absolute bg-white border flex flex-col w-full z-10" >
        <button v-for="value in posibleitems" @mousedown.prevent="changesearch(value)" class="p-2 text-start">{{value}}</button>
      </div>
    </div>

</template>
