<script setup lang="ts">
import { computed, defineComponent, h, ref, watch } from 'vue'
import Poper from '@/components/Zcrat/Elements/poper.vue'
import Checkbox from '@/components/Zcrat/Inputs/form/CheckboxFilter.vue'
import Search from '@/components/Zcrat/Inputs/Search.vue'
import { type option } from '@/types/generales'
import getMultiOptionFilter from '@/utils/functions/select/MultiOptionFilter'

interface Props {
  api: string
  params?: Record<string, any>
  label: string
}

const props = defineProps<Props>()
const selectedIds = defineModel<(number | string)[]>('selectedIds', { default: [] })

const options = ref<option[]>([])
const search = ref<string>('')
const loading = ref<boolean>(false)

const TextChild = defineComponent({
  props: {
    text: {
      type: String,
      required: true,
    },
  },
  setup(props) {
    return () => h('div', { class: 'p-2 text-sm text-gray-500' }, props.text)
  },
})

const filteredOptions = computed(() => {
  const value = search.value.trim().toLowerCase()

  if (!value) {
    return options.value
  }

  return options.value.filter((item) =>
    item.label.toLowerCase().includes(value)
  )
})

const hasSearch = computed(() => search.value.trim().length > 0)
const filteredOptionIds = computed(() => filteredOptions.value.map((item) => item.value))
const hasIncompleteVisibleSelection = computed(() => {
  if (selectedIds.value.length === 0) {
    return false
  }

  return filteredOptionIds.value.some((id) => !selectedIds.value.includes(id))
})
const showAllOption = computed(() => {
  if (loading.value) {
    return false
  }

  if (!hasSearch.value) {
    return selectedIds.value.length > 0
  }

  return filteredOptions.value.length > 0 && hasIncompleteVisibleSelection.value
})

const loadOptions = async () => {
  loading.value = true
  options.value = await getMultiOptionFilter(props.api, props.params ?? {})
  loading.value = false
}

watch(
  () => [props.api, props.params],
  loadOptions,
  { deep: true, immediate: true }
)

const ToggleIds = (id: string | number) => {
  if (selectedIds.value.length === 0) {
    selectedIds.value = options.value
      .map((item) => item.value)
      .filter((optionId) => optionId !== id)
    return
  }

  if (selectedIds.value.includes(id)) {
    selectedIds.value = selectedIds.value.filter((x) => x !== id)
    return
  }

  const newSelection = [...selectedIds.value, id]
  selectedIds.value = newSelection.length === options.value.length
    ? []
    : newSelection
}

const ToggleAll = () => {
  if (!hasSearch.value) {
    selectedIds.value = []
    return
  }

  selectedIds.value = Array.from(
    new Set([...selectedIds.value, ...filteredOptionIds.value])
  )
}

const textChild = (key: string, text: string) => ({
  key,
  element: TextChild,
  props: {
    text,
  },
})

const poperChildren = computed(() => [
  {
    key: 'search',
    element: Search,
    props: {
      modelValue: search.value,
      'onUpdate:modelValue': (value: string) => {
        search.value = value
      },
      Classdiv: 'w-full',
      placeholder: `Buscar ${props.label}`,
    },
  },
  
  ...(showAllOption.value ? [{
    key: 'all',
    element: Checkbox,
    props: {
      id: `all${props.label}`,
      checked: !hasSearch.value,
      name: `all${props.label}`,
      label: 'Todos',
      value: 'all',
      onChange: ToggleAll,
      classname: 'rounded-lg border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500',
    },
  }] : []),
  ...filteredOptions.value.map((item) => ({
    key: `${props.label}_${item.value}`,
    element: Checkbox,
    props: {
      id: `${props.label}Id_${item.value}`,
      checked:
        selectedIds.value.length === 0 ||
        selectedIds.value.includes(item.value),
      name: `${props.label}Id_${item.value}`,
      label: item.label,
      value: item.value,
      onChange: () => ToggleIds(item.value),
      classname: 'rounded-lg border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500',
    },
  })),
  ...(!loading.value && filteredOptions.value.length === 0
    ? [textChild('empty', 'Sin resultados')]
    : []),
])
</script>

<template>
  <Poper
    :father="label"
    :classname="''"
    :children="poperChildren"
  />
</template>
