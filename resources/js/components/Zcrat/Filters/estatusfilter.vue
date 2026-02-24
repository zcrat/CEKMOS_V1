<script setup lang="ts">
  import Checkbox from '@/components/Zcrat/Inputs/form/CheckboxFilter.vue'
  import Poper from '@/components/Zcrat/Elements/poper.vue' 
import { onMounted, ref,computed } from 'vue';
import { option } from '@/types/generales';
import GetEstatusFilter from '@/utils/functions/select2/EstatusFilter';
const estatus=ref<option[]>([])
const selectedIds = defineModel<(number|string)[]>('selectedIds', { default: [] })
onMounted(async () => {
    estatus.value = await GetEstatusFilter(2);
});
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
    if (newSelection.length === estatus.value.length) {
      selectedIds.value = []
    } else {
      selectedIds.value = newSelection
    }
  }
}
const poperChildren = computed(() => [
  {
    key: 'all',
    element: Checkbox,
    props: {
      id: 'allEstatusId',
      checked: selectedIds.value.length === 0,
      name: 'allEstatusId',
      label: 'Todos',
      value: 'all',
      onChange: () => ToggleIds('all'),
      classname: 'rounded-lg border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500',
    }
  },
  ...estatus.value.map((item) => ({
    key: 'estatus_' + item.value,   // 🔥 clave única obligatoria
    element: Checkbox,
    props: {
      id: 'estatusId_' + item.value,
      checked:
        selectedIds.value.length === 0 ||
        selectedIds.value.includes(item.value),
      name: 'estatusId_' + item.value,
      label: item.label,
      value: item.value,
      onChange: () => ToggleIds(item.value),
      classname: 'rounded-lg border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500',
    }
  }))
])
</script>
<template>
    
    <poper :father="'Estatus'" :classname="''"
                    :stoppropagation="true"
                    :align="'left'"
                    :children="poperChildren"

                />
</template>