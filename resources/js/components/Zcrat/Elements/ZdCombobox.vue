<script setup lang="ts">
import { ref,defineModel, watch } from 'vue'
import InputBasic from '../Inputs/form/InputBasic.vue'
import axios from 'axios'
import debounce from 'lodash/debounce';
const search = defineModel<string|null>()
const isactive = ref<boolean>(false);
const posibleitems = ref<string[]>([])
const inputRef = ref<HTMLElement | null>(null)
const abortrequest=ref<AbortController|null>(null);

const props = withDefaults(defineProps<{
  id: string
  endpoint: string
  placeholder?: string
  label?: string
  timeout?: number
  OnBlur?: () => void
  getallways?: boolean
}>(), {
  timeout: 500,
  placeholder: 'Buscar...',
  getallways: false
})

const loading = ref(false)

const GetOptions = async () => {
  if (abortrequest.value) {
    abortrequest.value.abort() // Cancelar la solicitud anterior
  }
  abortrequest.value = new AbortController() // Crear un nuevo AbortController
  try {
    loading.value = true
    const response = await axios.get(route(props.endpoint), {
      params: { search: search.value },
      signal: abortrequest.value.signal // Pasar la señal al fetch
    })
    posibleitems.value = response.data.options
  } catch (error) {
    console.error(error)
  } finally {
    loading.value = false
  }
}

const getdata = debounce(GetOptions, props.timeout);

const changesearch = (option:string) => {
  search.value=option;
  inputRef.value?.blur()
}
watch(search, () => {
  if ((search.value && search.value.length >= 2) || props.getallways) {
    getdata()
  } else {
    posibleitems.value = []
  }
})
</script>

<template>
    
    <div class="relative">
      <InputBasic :id="props.id" ref="inputRef" :label="props.label" type="text" v-model="search" :OnFocus="()=>isactive=true" :OnBlur="()=>{isactive=false; props.OnBlur?.()}"  :placeholder="props.placeholder"/>
      <div v-if="posibleitems.length > 0 && isactive" class="absolute bg-white border flex flex-col w-full z-10" >
        <button v-for="value in posibleitems" :key="value" @mousedown.prevent="changesearch(value)" class="p-2 text-start">{{value}}</button>
      </div>
    </div>

</template>
