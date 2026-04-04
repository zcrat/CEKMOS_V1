<script setup lang="ts">
import { ref, watch } from 'vue'
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
const oldvalue = ref()
const searched = ref(false)

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
    searched.value = true
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
watch([search,isactive], ([newSearch, newIsactive], [oldSearch, oldIsactive]) => {
  if (newIsactive && (newSearch!=oldSearch || props.getallways)) {
    getdata()
  }
})
function addItem(search: string) {
  if (search !== '' && !posibleitems.value.includes(search)) {
    posibleitems.value.push(search)
  }
}

</script>

<template>
    
    <div class="relative">
      <InputBasic :id="props.id" ref="inputRef" :label="props.label" type="text" v-model="search" :OnFocus="()=>{isactive=true; oldvalue=search}" :OnBlur="()=>{
        isactive=false; 
        if(oldvalue !== search){
          props.OnBlur?.()
        }
        //if(search && !posibleitems.find((item) => item === search )){addItem(search)}
        }"
      :placeholder="props.placeholder"/>
      <div v-if="isactive" class="absolute bg-white border-2 border-[--micolor] rounded mt-2 flex flex-col w-full z-10 max-h-[15rem] overflow-auto" tabindex="-1">
        <div v-if="loading" class="p-2 text-center">
          Cargando...
        </div class="w-full">
        <div v-else>    
          <div v-if="posibleitems.length > 0">
            <button v-for="value in posibleitems" :key="value" @mousedown.prevent="changesearch(value)" :class="['p-2 text-start w-full',search === value ? 'bg-[--micolor] opacity-80  text-white font-semibold' : 'hover:bg-gray-200']"  tabindex="-1">{{value}}</button>
          </div>
          <div v-else-if="searched" class="p-2 text-center text-gray-500" >
            Sin Coincidencias...
          </div>
          <div v-else class="p-2 text-center text-gray-500" >
            Escribe para buscar...
          </div>
        </div>
      </div>
    </div>

</template>
