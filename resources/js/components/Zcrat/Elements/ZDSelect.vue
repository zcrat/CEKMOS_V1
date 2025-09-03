<script setup lang="ts">
import { ref, watch } from 'vue'
import { Combobox, ComboboxInput, ComboboxOptions, ComboboxOption } from '@headlessui/vue'
import { CheckIcon } from '@heroicons/vue/20/solid'
import axios from 'axios'

interface Option {
  value: number | string
  label: string
}

const props = withDefaults(defineProps<{
  id:string
  endpoint: string
  type: string
  default_option?: Option
  modelValue: Option | null
  placeholder?:string
}>(), {
  placeholder:'Buscar...'
})

const options = ref<Option[]>([])
const loading = ref(false)
const optionselect = ref<Option | null>(props.default_option ?? null)
const query = ref('')
const isOpen = ref(false)

const GetOptions = async () => {
  try {
    loading.value = true
    const response = await axios.get(route(props.endpoint), { params: { query: query.value } })
    options.value = response.data.options
  } catch (error: any) {
    console.error(error)
  } finally {
    loading.value = false
  }
}

let timeout: ReturnType<typeof setTimeout>
watch(query, () => {
  clearTimeout(timeout)
  timeout = setTimeout(() => { GetOptions() }, 400)
})

const onFocus = () => {
  isOpen.value = true
  query.value = ''
  GetOptions()
}

const emit = defineEmits(['update:modelValue'])

watch(optionselect, (val) => {
  emit('update:modelValue', val)
})

watch(() => props.modelValue, (val) => { optionselect.value = val }, { immediate: true })

const selectOption = () => {
  const input = document.querySelector('#'+props.id) as HTMLInputElement
  input?.blur()
}
</script>

<template>
  <Combobox v-model="optionselect">
    <div class="relative">
      <div>
        <ComboboxInput
          class="w-full ps-2 pr-6 truncate rounded border-2 ComboboxInput"
          :id="id"
          @change="query = $event.target.value"
          :placeholder="placeholder"
          @focus="onFocus"
          @blur="isOpen = false"
          :displayValue="(option: unknown) => (option as Option | null)?.label ?? ''"
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
      <ComboboxOptions v-show="isOpen" static class="absolute border-2 border-gray-500 w-full bg-white p-2 z-50">
        <div v-if="options.length === 0">Sin Resultados</div>
        <ComboboxOption
          v-for="option in options"
          :key="option.value"
          :value="option"
          @click="selectOption()"
          :disabled="option.value === optionselect?.value"
          v-slot="{ selected, active }"
          as="template"
        >
          <li
            class="relative cursor-default select-none py-2 pl-10 pr-4"
            :class="{ 'bg-teal-600 text-white': active, 'text-gray-900': !active }"
          >
            <span :class="{ 'font-medium': selected, 'font-normal': !selected }" class="block truncate">
              {{ option.label }}
            </span>
            <span
              v-if="option.value === optionselect?.value"
              class="absolute inset-y-0 left-0 flex items-center pl-3"
              :class="{ 'text-white': active, 'text-teal-600': !active }"
            >
              <CheckIcon class="h-5 w-5" aria-hidden="true" />
            </span>
          </li>
        </ComboboxOption>
      </ComboboxOptions>
    </div>
  </Combobox>
</template>
