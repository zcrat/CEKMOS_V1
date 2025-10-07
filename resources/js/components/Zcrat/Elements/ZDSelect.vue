<script setup lang="ts">
import { ref, watch } from 'vue'
import { Combobox, ComboboxInput, ComboboxOptions, ComboboxOption } from '@headlessui/vue'
import axios from 'axios'
import { useDebounceFn } from '@vueuse/core'
import { type option } from '@/utils/interfaces/generales'

const props = withDefaults(defineProps<{
  id:string
  endpoint: string
  new_option?: option | null
  placeholder?:string
  label?: string
  timeout?: number
}>(), {
  timeout: 400,
  placeholder:'Buscar...'
})
const selected = defineModel<string | number |null>()
const options = ref<option[]>([])
const optionselect = ref<option | null>(null)
const loading = ref(false)
const query = ref('')
const isOpen = ref(false)
const thisid=props.id+Date.now()
let controller: AbortController | null = null

const GetOptions = async () => {
  try {
    // Cancelar solicitud anterior si existe
    if (controller) controller.abort()
    controller = new AbortController()

    loading.value = true
    const response = await axios.get(route(props.endpoint), { 
      params: { query: query.value },
      signal: controller.signal  // <-- pasar el signal al fetch
    })
    options.value = response.data.options
  } catch (error: any) {
    if (error.name === 'CanceledError') return  // solicitud cancelada
    console.error(error)
  } finally {
    loading.value = false
  }
}

const debouncedGetOptions = useDebounceFn(GetOptions, props.timeout)

watch(query, debouncedGetOptions)

const onFocus = () => {
  isOpen.value = true
  //query.value = '' opcional para resetear la busqueda al enfocar
  GetOptions()
}
const selectOption = () => {
  const input = document.querySelector('#'+thisid) as HTMLInputElement
  input?.blur()
}
watch(() => props.new_option, (val) => {
  if(val != undefined){
    if (!options.value.find(o => o.value === val.value) && val != null) {
      options.value.push(val)
    }
    optionselect.value = val;
  }
}, { immediate: true })

watch(optionselect, (val) => {
  selected.value = val ? val.value : null
})

watch(selected, (val) => {
  if (val == null || val === undefined) {
    optionselect.value = null
  } else {
    if (!options.value.find(o => o.value === val)) {
        
    }
    const found = options.value.find(o => o.value === val)
    if (found) {
      optionselect.value = found
    } else {
      const tempOption = { value: val, label: String(val) }
      options.value.push(tempOption)
      optionselect.value = tempOption
    }
  }
})
</script>

<template>
  <div class="flex flex-col w-full">
    <label for="" v-if="props.label">{{ props.label }}</label>
    <Combobox v-model="optionselect">
      <div class="relative">
        <div>
          <ComboboxInput
          class="w-full ps-2 pr-6 truncate rounded border-2 ComboboxInput"
          :id="thisid"
          @change="query = $event.target.value"
          :placeholder="placeholder"
          @focus="onFocus"
          @blur="isOpen = false"
          :displayValue="(option: unknown) => (option as option | null)?.label ?? ''"
          />
        <button
          v-if="optionselect"
          type="button"
          class="absolute inset-y-0 right-2 text-sm flex items-center text-gray-400 hover:text-gray-600"
          @click="optionselect = null"
        >
          ✕
        </button>
      </div>
      <ComboboxOptions v-show="isOpen" static class="absolute border-2 border-gray-500 w-full rounded-md bg-white z-50">
        <div v-if="options.length === 0 && !loading">Sin Resultados</div>
        <div v-if="options.length === 0 && loading">Cargando...</div>
        <ComboboxOption
          v-for="option in options"
          :key="option.value"
          :value="option"
          @click="selectOption()"
          
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
