<script setup lang="ts">
import { onMounted, ref, watch,onBeforeUnmount } from 'vue'

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
  ComboboxPortal
} from 'reka-ui'
import ZDListErrors from './ZDListErrors.vue'
import ZDIconError from './ZDIconError.vue'

const props = withDefaults(defineProps<{
    endpoint: string
    buttonNew?:()=>void
    canNew?:boolean
    searchable?:boolean
    clearable?:boolean
    cacheoptions?:boolean
    placeholder?:string
    label?: string
    timeout?: number
    params?:Record<string,any>
    empty_message?:string
    loading_message?:string
    close_when_selected?:boolean
    DeleteErrors?: () => void
    errors?: string[]
}>(), {
  timeout: 400,
  canNew:false,
  placeholder:'Buscar...',
  empty_message:'Sin Resultados',
  loading_message:'Cargando...',
  searchable:true,
  clearable:true,
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
const inputRef = ref<HTMLInputElement | null>(null)
const onSelect = () => {
    query.value = ''
    if(props.close_when_selected){
        isOpen.value = false
    }
}
const onFocus = () => {
  console.log('focus')
  isOpen.value = true
  if(!props.cacheoptions){
    GetOptions()
  }
}
const onMouseDown = (e: MouseEvent) => {
  if (isOpen.value) {
    e.preventDefault() // 👈 evita que vuelva a hacer focus
    isOpen.value = false
  } else {
    isOpen.value = true
  }
}
onMounted(()=>{
  GetOptions()
})
watch(query, debouncedGetOptions)

watch(isOpen, (open) => {
  if (!open) { 
    query.value = ''
    inputRef.value?.blur() // quitar foco del input
  }
})
watch(() => props.params, (newVal, oldVal) => {
  if (!isEqual(newVal, oldVal)) {
    GetOptions()
  }
})
watch(optionselect, () => {
  props.DeleteErrors?.();
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
const handleScroll = (e: Event) => {
  const target = e.target as HTMLElement;

  // 🔥 ignorar scroll del dropdown
  if (target.closest('.select2-dropdown')) return;
  
  // opcional: ignorar input
  if (target === inputRef.value) return;

  if (isOpen.value) {
    isOpen.value = false;
  }
};

onMounted(() => {
  window.addEventListener('scroll', handleScroll, true) // 👈 CAPTURE
})

onBeforeUnmount(() => {
  window.removeEventListener('scroll', handleScroll, true)
})
</script>

<template>
  <div class="flex flex-col w-full relative" >
    <label for="" v-if="props.label">{{ props.label }}</label>
  <ComboboxRoot class="relative" v-model="optionselect" v-model:open="isOpen">
    <ComboboxAnchor 
      :class="['inline-flex w-full relative border border-black rounded-md',{ inputfocusalways: isOpen },{inputerror: props.errors && props.errors.length > 0}]"
      >
      <ComboboxInput asChild>
        <div class="relative flex items-center w-full">
          <button class="px-2 h-full border-r border-black flex items-center" v-if="buttonNew" @click="buttonNew"><font-awesome-icon :icon="optionselect ? 'fa-regular fa-pen-to-square': 'fa-solid fa-plus' "  :class="optionselect? 'text-xl':''"/></button>
          <div class="relative flex w-full">
            <ZDIconError :errors="props.errors"/>
            <input
            ref="inputRef"
            :class="['w-full ps-2 pr-8 rounded border-none inputnotfocus',
                        props.errors && props.errors.length > 0 ? 'ps-[2rem]' : ''
                    ]"
            @input="onInputChange"
            :readonly="props.searchable === false"
            :placeholder="placeholder"
            @focus="onFocus"
            :value="isOpen ? query : optionselect?.label ?? ''"
            @mousedown="onMouseDown"
            @blur="()=>{console.log('blurr')}"
          />
          </div>
        </div>
      </ComboboxInput>
      <ComboboxTrigger class="absolute inset-y-0 right-0 px-2 w-8" v-if="!optionselect || !clearable">
        <font-awesome-icon icon="fa-solid fa-angle-down" class="text-[1.25rem]" v-if="!isOpen"/>
        <font-awesome-icon icon="fa-solid fa-angle-up" class="text-[1.25rem]" v-else/>
      </ComboboxTrigger>
      <ComboboxCancel class="absolute inset-y-0 right-0 px-2 w-8" v-if="optionselect && clearable && !isOpen" @click="clearSelect">
        <font-awesome-icon icon="fa-solid fa-x" class="text-[0.8rem]"/>
      </ComboboxCancel>
    </ComboboxAnchor>

    <ComboboxPortal>
    <ComboboxContent 
      position="popper"
      :avoidCollisions="true"
      :collisionPadding="8"
      :sideOffset="4"
      class="z-15 w-[var(--reka-combobox-trigger-width)] bg-white border-2 border-gray-500 rounded-md select2-dropdown">
      <ComboboxViewport>
        <div v-if="loading" class="text-mauve8 text-xs font-medium text-center py-2">
          {{ loading_message }}
        </div>
        <template v-else>
        <ComboboxEmpty class="text-mauve8 text-xs font-medium text-center py-2">
          {{empty_message }}
        </ComboboxEmpty>

        <ComboboxGroup class="max-h-[25rem] overflow-auto">
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
        </template>
      </ComboboxViewport>
    </ComboboxContent>
    </ComboboxPortal>
  </ComboboxRoot>
   <ZDListErrors :errors="props.errors"/>
  </div>
</template>