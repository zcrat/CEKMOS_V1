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

const search = defineModel<string | null>()

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

const isOpen = ref(false)
const posibleitems = ref<string[]>([])
const loading = ref(false)
const searched = ref(false)
const abortrequest = ref<AbortController | null>(null)
const oldvalue = ref()
const inputRef = ref<HTMLInputElement | null>(null)

const GetOptions = async () => {
  if (abortrequest.value) {
    abortrequest.value.abort()
  }
  abortrequest.value = new AbortController()

  try {
    loading.value = true
    const response = await axios.get(route(props.endpoint), {
      params: { search: search.value },
      signal: abortrequest.value.signal
    })
    posibleitems.value = response.data.options
    searched.value = true
  } catch (e) {
    console.error(e)
  } finally {
    loading.value = false
  }
}

const getdata = debounce(GetOptions, props.timeout)

watch(search, () => {
  console.log('Search changed:', search.value)
  if (isOpen.value && (search.value || props.getallways)) {
    getdata()
  }
})

// 🔥 SELECT
const selectItem = (value: string) => {
  search.value = value
  isOpen.value = false
  inputRef.value?.blur()
}

// 🔥 FOCUS / BLUR
const onFocus = () => {
  isOpen.value = true
  oldvalue.value = search.value
}

const onBlur = () => {
  //isOpen.value = false
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
  <div class="flex flex-col w-full">
    
    <label v-if="label">{{ label }}</label>

    <ComboboxRoot v-model:open="isOpen">

      <!-- INPUT -->
      <ComboboxAnchor class="relative w-full">
        <ComboboxInput asChild>
          <input
            ref="inputRef"
            type="text"
            :value="search"
            @input="onInputChange"
            :placeholder="placeholder"
            class="w-full border rounded px-2 py-2"
            @focus="onFocus"
            @blur="onBlur"
          />
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
              <ComboboxEmpty v-if="searched && posibleitems.length === 0">
                <div class="p-2 text-center text-gray-500">
                  Sin Coincidencias...
                </div>
              </ComboboxEmpty>

              <div v-else-if="!searched">
                <div class="p-2 text-center text-gray-500">
                  Escribe para buscar...
                </div>
              </div>

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
  </div>
</template>