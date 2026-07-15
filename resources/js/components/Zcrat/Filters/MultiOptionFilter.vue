<script setup lang="ts">
import Checkbox from '@/components/Zcrat/Inputs/form/CheckboxFilter.vue'
import Poper from '@/components/Zcrat/Elements/poper.vue'
import Search from '@/components/Zcrat/Inputs/Search.vue'
import { type option } from '@/types/generales'
import getMultiOptionFilter from '@/utils/functions/select/MultiOptionFilter'
import { computed, defineComponent, h, ref, watch } from 'vue'

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

const sameIds = (currentIds: (number | string)[], nextIds: (number | string)[]) =>
  currentIds.length === nextIds.length &&
  currentIds.every((id, index) => id === nextIds[index])

const setSelectedIds = (nextIds: (number | string)[]) => {
  if (sameIds(selectedIds.value, nextIds)) {
    return
  }

  selectedIds.value = nextIds
}

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
const loadOptions = async () => {
  loading.value = true
  options.value = await getMultiOptionFilter(props.api, props.params ?? {})
  loading.value = false
}
watch(() => [props.api, props.params], loadOptions, { deep: true, immediate: true })

const filteredOptions = computed(() => {
  const value = search.value.trim().toLowerCase()
  if (!value) {
    return options.value
  }
  return options.value.filter((item) => item.label.toLowerCase().includes(value))
})
const optionIds = computed(() => options.value.map((item) => item.value))
const filteredOptionIds = computed(() => filteredOptions.value.map((item) => item.value))
const hasSearch = computed(() => search.value.trim().length > 0)
const hasDifferentFilteredOptions = computed(() => {
  if (!hasSearch.value) {
    return false
  }

  return (
    filteredOptionIds.value.length !== optionIds.value.length ||
    optionIds.value.some((id) => !filteredOptionIds.value.includes(id))
  )
})
const hasSelectedOutsideFilteredOptions = computed(() =>
  selectedIds.value.some((id) => !filteredOptionIds.value.includes(id))
)
const showOnlyFilteredOption = computed(() =>
  hasDifferentFilteredOptions.value &&
  filteredOptions.value.length > 0 &&
  (selectedIds.value.length === 0 || hasSelectedOutsideFilteredOptions.value)
)
const allSelected = computed(() => {
  if (options.value.length === 0) {
    return true
  }
  if (selectedIds.value.length === 0) {
    return true
  }

  const ids = hasSearch.value ? filteredOptionIds.value : optionIds.value

  return ids.length > 0 && ids.every((id) => selectedIds.value.includes(id))
})

const showAllOption = computed(() => {
  if (loading.value) {
    return false
  }

  if (options.value.length === 0) {
    return false
  }

  if (!hasSearch.value) {
    return options.value.length > 1 && selectedIds.value.length > 0
  }

  if (filteredOptions.value.length <= 1) {
    return false
  }

  return selectedIds.value.length > 0 && !allSelected.value
})

const toggleId = (id: string | number) => {
  if (selectedIds.value.length === 0) {
    setSelectedIds(options.value
      .map((item) => item.value)
      .filter((optionId) => optionId === id))
    return
  }

  if (selectedIds.value.includes(id)) {
    setSelectedIds(selectedIds.value.filter((optionId) => optionId !== id))
    return
  }

  const newSelection = [...selectedIds.value, id]
  setSelectedIds(newSelection.length === options.value.length ? [] : newSelection)
}

const toggleAll = () => {
  if (options.value.length === 0) {
    setSelectedIds([])
    return
  }

  if (hasSearch.value && filteredOptions.value.length === 0) {
    setSelectedIds([])
    return
  }

  if (!hasSearch.value) {
    setSelectedIds([])
    return
  }

  const visibleIds = filteredOptionIds.value

  if (allSelected.value) {
    setSelectedIds(selectedIds.value.length === 0
      ? options.value
          .map((item) => item.value)
          .filter((id) => !visibleIds.includes(id))
      : selectedIds.value.filter((id) => !visibleIds.includes(id)))
    return
  }

  const newSelection = Array.from(new Set([...selectedIds.value, ...visibleIds]))
  setSelectedIds(newSelection.length === options.value.length ? [] : newSelection)
}

const toggleOnlyFiltered = () => {
  setSelectedIds([...filteredOptionIds.value])
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
  ...(showOnlyFilteredOption.value
    ? [
        {
          key: 'only-filtered',
          element: Checkbox,
          props: {
            id: `onlyFiltered${props.label}`,
            checked: false,
            name: `onlyFiltered${props.label}`,
            label: 'Solo las mostradas',
            value: 'only-filtered',
            onChange: toggleOnlyFiltered,
            classname:
              'rounded-lg border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500',
          },
        },
      ]
    : []),
  ...(showAllOption.value
    ? [
        {
          key: 'all',
          element: Checkbox,
          props: {
            id: `all${props.label}`,
            checked: allSelected.value,
            name: `all${props.label}`,
            label: hasSearch.value && filteredOptions.value.length > 0
              ? 'Todos Los Encontrados'
              : 'Todos',
            value: 'all',
            onChange: toggleAll,
            classname:
              'rounded-lg border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500',
          },
        },
      ]
    : []),
  ...filteredOptions.value.map((item) => ({
    key: `${props.label}_${item.value}`,
    element: Checkbox,
    props: {
      id: `${props.label}Id_${item.value}`,
      checked: selectedIds.value.length === 0 || selectedIds.value.includes(item.value),
      name: `${props.label}Id_${item.value}`,
      label: item.label,
      value: item.value,
      onChange: () => toggleId(item.value),
      classname: 'rounded-lg border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500',
    },
  })),
  ...(!loading.value && filteredOptions.value.length === 0
    ? [textChild('empty', 'Sin resultados')]
    : []),
])
</script>

<template>
  <Poper :father="label" :classname="''" :children="poperChildren" />
</template>
