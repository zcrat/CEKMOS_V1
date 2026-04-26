<script setup lang="ts">
import { ref, watch } from 'vue'
import axios from 'axios'
import debounce from 'lodash/debounce'
import {
  ComboboxRoot,
  ComboboxInput,
  ComboboxAnchor,
  ComboboxContent,
  ComboboxViewport,
  ComboboxItem,
  ComboboxEmpty,
  ComboboxPortal
} from 'reka-ui'
import ZDListErrors from './ZDListErrors.vue'
import ZDIconError from './ZDIconError.vue'
import { Delete } from 'lucide-vue-next'

const search = defineModel<string | null>()

const props = withDefaults(defineProps<{
  id: string
  endpoint: string
  placeholder?: string
  label?: string
  timeout?: number
  OnBlur?: () => void
  getallways?: boolean,
  errors?: string[],
  DeleteErrors?: ()=>void
}>(), {
  timeout: 500,
  placeholder: 'Buscar...',
  getallways: false
})

const isOpen = ref(false)
const posibleitems = ref<string[]>([])
const loading = ref(false)
const abortrequest = ref<AbortController | null>(null)
const oldvalue = ref()
const inputRef = ref<HTMLInputElement | null>(null)

const GetOptions = async ({executeAxios}:{executeAxios:boolean}) => {
  if (abortrequest.value) {
    abortrequest.value.abort()
  }
  abortrequest.value = new AbortController()
  
  try {
    if(executeAxios){
      loading.value = true
      const searchTerm = search.value || ''
      const response = await axios.get(route(props.endpoint), {
        params: { search: searchTerm },
        signal: abortrequest.value.signal
      })
      if(searchTerm == search.value){ 
        posibleitems.value = response.data.options
      }
    }else{
      posibleitems.value = []
    }
  } catch (e) {
    console.error(e)
  } finally {
    loading.value = false
  }
}

const getdata = debounce(({executeAxios = true}:{executeAxios:boolean}) => GetOptions({ executeAxios }), props.timeout)

watch(search, () => {
  if (isOpen.value && (search.value || props.getallways)) {
    loading.value = true
    getdata({executeAxios:true})
  }else {
    getdata({executeAxios:false})
  }
    props.DeleteErrors?.()
})
watch(isOpen, (newValue) => {
  if(!newValue){
    inputRef.value?.blur()
    onBlur()
  }
})

const selectItem = (value: string) => {
  search.value = value
  isOpen.value = false
}

const onFocus = () => {
  isOpen.value = true
  oldvalue.value = search.value
}
const onBlur = () => {
  if(oldvalue.value !== search.value){
    props.OnBlur?.()
  }
  //if(search && !posibleitems.find((item) => item === search )){addItem(search)}
}
const onInputChange=(event: Event)=> {
  search.value = (event.target as HTMLInputElement).value;
}
</script>
<template>
  <div class="flex flex-col w-full relative">
    
    <label v-if="label">{{ label }}</label>

    <ComboboxRoot v-model:open="isOpen">

      <!-- INPUT -->
      <ComboboxAnchor class="relative w-full">
        <ComboboxInput asChild>
          <div class="relative">
            <ZDIconError :errors="props.errors"/>
            <input
            ref="inputRef"
            type="text"
            :value="search"
            @input="onInputChange"
            :placeholder="placeholder"
            :class="['w-full border rounded px-2 py-2',
            props.errors && props.errors.length > 0 ? 'inputerror ps-[2rem]' : '']"
            @focus="onFocus"
            />
          </div>
        </ComboboxInput>
      </ComboboxAnchor>

      <!-- DROPDOWN -->
      <ComboboxPortal>
        <ComboboxContent
          position="popper"
          side="bottom"
          align="start"
          :sideOffset="4"
          :avoidCollisions="true"
          class="z-50 w-[var(--reka-combobox-trigger-width)] bg-white border rounded shadow-md"
        >
          <ComboboxViewport class="max-h-[15rem] overflow-auto">

            <!-- LOADING -->
            <div v-if="loading" class="p-2 text-center">
              Cargando...
            </div>
            <!-- ITEMS -->
            <template v-else>
              <template v-if=" posibleitems.length === 0">
                <ComboboxEmpty v-if="search">
                  <div class="p-2 text-center text-gray-500">
                    Sin Coincidencias...
                  </div>
                </ComboboxEmpty>

                <div v-else>
                  <div class="p-2 text-center text-gray-500">
                    Escribe para buscar...
                  </div>
                </div>
              </template>
              <ComboboxItem
                v-for="item in posibleitems"
                :key="item"
                :value="item"
                @select="selectItem(item)"
                as="template"
              >
                <div class="px-3 py-2 cursor-pointer hover:bg-gray-200">
                  {{ item }}
                </div>
              </ComboboxItem>
            </template>

          </ComboboxViewport>
        </ComboboxContent>
      </ComboboxPortal>

    </ComboboxRoot>
    <ZDListErrors :errors="props.errors"/>
  </div>
</template>