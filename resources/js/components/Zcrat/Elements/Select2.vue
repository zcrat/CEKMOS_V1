<script setup lang="ts">
import { computed, ref, watch } from 'vue'
import { Combobox, ComboboxInput, ComboboxOptions, ComboboxOption } from '@headlessui/vue'
import axios from 'axios'
import { useDebounceFn } from '@vueuse/core'
import { type option } from '@/types/generales'
import isEqual from 'lodash/isEqual'

const props = withDefaults(defineProps<{
    endpoint: string
    canNew?:boolean
    searchable?:boolean
    cleareble?:boolean
    cacheoptions?:boolean
    placeholder?:string
    label?: string
    timeout?: number
    params?:Record<string,any>
    empty_message?:string
    loading_message?:string
    close_when_selected?:boolean
    blur_when_selected?:boolean
}>(), {
  timeout: 400,
  canNew:false,
  placeholder:'Buscar...',
  empty_message:'Sin Resultados',
  loading_message:'Cargando...',
  searchable:true,
  cleareble:true,
  cacheoptions:true,
  close_when_selected:true,
  blur_when_selected:true
})
const options = ref<option[]>([])
const optionselect = defineModel<option | null>()
const loading = ref<boolean>(false)
const query = ref<string>('')
const isOpen = ref<boolean>(false)
const thisid='Select2'+Date.now()

let controller: AbortController | null = null
let currentRequestId = 0

const GetOptions = async () => {
  let requestId: number | null = null
  try {
    // Cancelar solicitud anterior si existe
    if (controller) controller.abort()
    controller = new AbortController()
    requestId = ++currentRequestId

    loading.value = true
    const response = await axios.get(route(props.endpoint), { 
      params: { query: query.value , ...props.params},
      signal: controller.signal  // <-- pasar el signal al fetch
    })
    if (requestId === currentRequestId) {
      options.value = response.data.options
    }
  } catch (error: any) {
    if (error.name === 'CanceledError') return  // solicitud cancelada
    console.error(error)
  } finally {
    if (currentRequestId === requestId) {
      loading.value = false
    }
  }
}
const debouncedGetOptions = useDebounceFn(GetOptions, props.timeout)
const triggerBlur = () => {
    query.value = ''
    if(props.blur_when_selected){
        const input = document.querySelector('#'+thisid) as HTMLInputElement
        input?.blur()
    }else if(props.close_when_selected){
        isOpen.value = false
    }
}
const onFocus = () => {
  isOpen.value = true
  if(!props.cacheoptions){
    GetOptions()
  }
}
const onBlur = () => {
  isOpen.value = false
}
watch(query, debouncedGetOptions)

watch(() => props.params, (newVal, oldVal) => {
  if (!isEqual(newVal, oldVal)) {
    GetOptions()
  }
})

const onInputChange=(event: Event)=> {
  if (props.searchable === false) {
    event.preventDefault();
    return;
  }
  query.value = (event.target as HTMLInputElement).value;
}
</script>

<template>
  <div class="flex flex-col w-full" >
    <label for="" v-if="props.label">{{ props.label }}</label>
    <Combobox v-model="optionselect">
      <div class="relative">
        <div>
          <ComboboxInput
          :id="thisid"
          class="w-full ps-2 pr-6 truncate rounded border-2 ComboboxInput"
          @change="onInputChange"
          :readonly="props.searchable === false"
          :placeholder="placeholder"
          @blur="onBlur"
          @focus="onFocus"
          :displayValue="(option: unknown) => (option as option | null)?.label ?? ''"
          />
        <button
          v-if="optionselect && cleareble"
          type="button"
          class="absolute inset-y-0 right-2 text-sm flex items-center text-gray-400 hover:text-gray-600"
          @click="optionselect = null"
        >
          ✕
        </button>
      </div>
      <ComboboxOptions v-show="isOpen" static class="absolute border-2 border-gray-500 w-full rounded-md bg-white z-50 overflow-auto max-h-[15rem]">
        <div v-if="options.length === 0 && !loading">{{props.empty_message }}</div>
        <div v-if="loading">{{props.loading_message}}</div>
        <ComboboxOption
          v-else
          v-for="option in options"
          :key="option.value"
          :value="option"
          @click="triggerBlur"
          v-slot="{ active }"
          as="template"
        >
          <div :class="['px-4 py-2 rounded-sm ', active && option.value != optionselect?.value ? 'bg-blue-500 text-white hover:cursor-pointer' : 'text-gray-700' , option.value === optionselect?.value ? 'font-semibold bg-green-200 hover:cursor-default' : 'font-normal']">
            {{ option.label }}
          </div>

        </ComboboxOption>
      </ComboboxOptions>
    </div>
  </Combobox>
  </div>
</template>
