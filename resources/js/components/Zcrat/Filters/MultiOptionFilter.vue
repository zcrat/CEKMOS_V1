<script setup lang="ts">
  import Checkbox from '@/components/Zcrat/Inputs/form/CheckboxFilter.vue'
  import Poper from '@/components/Zcrat/Elements/poper.vue' 
import { onMounted, ref,computed, watch } from 'vue';
import { option } from '@/types/generales';
import getMultiOptionFilter from '@/utils/functions/select/MultiOptionFilter';
import Search from '../Inputs/Search.vue';
interface Props {
    api: string;
    params?: Record<string,any>;
    label:string
} 

const props = defineProps<Props>();
const options=ref<option[]>([])
const selectedIds = defineModel<(number|string)[]>('selectedIds', { default: [] })
const search = ref<string>('')

const loadOptions = async () => {
  options.value = await getMultiOptionFilter(props.api, props.params ?? {})
  
}

watch( 
  () => [props.api, props.params],
  loadOptions,
  { deep: true, immediate: true }
)
const filteredOptions = computed(() => {
  const value = search.value.trim().toLowerCase()

  if (!value) {
    return options.value
  }

  return options.value.filter((item) =>
    item.label.toLowerCase().includes(value)
  )
})
const ToggleIds = (id: string | number) => {
  if (id === "all") {
    selectedIds.value = []
    return
  }
  if (selectedIds.value.length === 0) {
    selectedIds.value = [id]
    return
  }

  if (selectedIds.value.includes(id)) {
    selectedIds.value = selectedIds.value.filter(x => x !== id)
  } else {
    const newSelection = [...selectedIds.value, id]
    if (newSelection.length === options.value.length) {
      selectedIds.value = []
    } else {
      selectedIds.value = newSelection
    }
  }
}
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
  {
    key: 'all',
    element: Checkbox,
    props: {
      id: 'all'+props.label,
      checked: selectedIds.value.length === 0 || (search.value != '' ),
      name: 'all'+props.label,
      label: 'Todos',
      value: 'all',
      onChange: () => ToggleIds('all'),
      classname: 'rounded-lg border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500',
    }
  },
  ...options.value.map((item) => ({
    key: props.label+'_' + item.value,   // 🔥 clave única obligatoria
    element: Checkbox,
    props: {
      id: props.label+'Id_' + item.value,
      checked:
        selectedIds.value.length === 0 ||
        selectedIds.value.includes(item.value),
      name: props.label+'Id_' + item.value,
      label: item.label,
      value: item.value,
      onChange: () => ToggleIds(item.value),
      classname: 'rounded-lg border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500',
    }
  }))
])
</script>
<template>
    
    <poper :father="label" :classname="''"
                    :stoppropagation="true"
                    :align="'left'"
                    :children="poperChildren"

                />
</template>