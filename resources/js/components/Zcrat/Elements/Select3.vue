<script setup lang="ts">
import { onMounted, ref, watch } from 'vue'

import axios from 'axios'
import { useDebounceFn } from '@vueuse/core'
import { type option } from '@/types/generales'
import isEqual from 'lodash/isEqual'
import {
  ComboboxAnchor,
  ComboboxContent,
  ComboboxEmpty,
  ComboboxGroup,
  ComboboxInput,
  ComboboxItem,
  ComboboxRoot,
  ComboboxTrigger,
  ComboboxViewport,
  ComboboxCancel,
} from 'reka-ui'

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
})
const options = ref<option[]>([])
const optionselect = defineModel<option | null>()
const loading = ref<boolean>(false)
const query = ref<string>('')
const isOpen = ref<boolean>(false)

let controller: AbortController | null = null
let currentRequestId = 0

const GetOptions = async () => {
  let requestId: number | null = null
  try {
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

const onSelect = () => {
    query.value = ''
    if(props.close_when_selected){
        isOpen.value = false
    }
}
const onFocus = () => {
  isOpen.value = true
  if(!props.cacheoptions){
    GetOptions()
  }
}
onMounted(()=>{
  GetOptions()
})
watch(query, debouncedGetOptions)

watch(isOpen, (open) => {
  if (!open) query.value = ''
})
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
const clearSelect = () => {
  query.value = ''
  optionselect.value = null
}
</script>

<template>
  <div class="flex flex-col w-full" >
    <label for="" v-if="props.label">{{ props.label }}</label>
  <ComboboxRoot class="relative" v-model="optionselect" v-model:open="isOpen">
    <ComboboxAnchor 
      :class="['inline-flex w-full relative border border-black rounded-md',{ inputfocusalways: isOpen }]"
      >
      <ComboboxInput
        :class="['w-full ps-2 pr-6 truncate rounded border-none inputnotfocus']"
        @input="onInputChange"
        :readonly="props.searchable === false"
        :placeholder="placeholder"
        @focus="onFocus"
        :displayValue="(option: option | null) => option?.label ?? ''"
      />
      <ComboboxTrigger class="absolute inset-y-0 right-2" v-if="!optionselect || !cleareble">
        <font-awesome-icon icon="fa-solid fa-angle-down" class="text-[1.25rem]" v-if="!isOpen"/>
        <font-awesome-icon icon="fa-solid fa-angle-up" class="text-[1.25rem]" v-else/>
      </ComboboxTrigger>
      <ComboboxCancel class="absolute inset-y-0 right-2" v-if="optionselect && cleareble && !isOpen" @click="clearSelect">
        <font-awesome-icon icon="fa-solid fa-x" class="text-[0.8rem]"/>
      </ComboboxCancel>
    </ComboboxAnchor>

    <ComboboxContent 
      class="absolute z-10 w-full mt-1 min-w-[10rem] bg-white  border-2 border-gray-500 overflow-y-auto rounded-md ">
      <ComboboxViewport>
        <ComboboxEmpty class="text-mauve8 text-xs font-medium text-center py-2">
          {{loading? loading_message :empty_message }}
        </ComboboxEmpty>

        <ComboboxGroup v-if="!loading">
          <ComboboxItem
            v-for="option in options"
            :key="option.value"
            :value="option"
            @select="onSelect"
            as="template"
            > 
            <div :class="['px-4 py-2 rounded-sm ', option.value === optionselect?.value ? 'optionactive' : 'hoveroptionselect']">
              {{ option.label }}
            </div>
          </ComboboxItem>
        </ComboboxGroup>
      </ComboboxViewport>
    </ComboboxContent>
  </ComboboxRoot>
  </div>
</template>