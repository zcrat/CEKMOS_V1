<script setup lang="ts">
import {ref, watch,onMounted,onBeforeUnmount } from 'vue'
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
import { type option } from '@/types/generales'

const props = withDefaults(defineProps<{
  id?:string
  label?: string
  placeholder?:string
  empty_message?:string
  clearable?:boolean
  searchable?:boolean
  disabled?:boolean
  close_when_selected?:boolean
  options:option[]
}>(), {
  empty_message:'Sin Opciones',
  placeholder:'Seleccionar',
  searchable:false,
  clearable:false,
  close_when_selected:true,
})
const selected = defineModel<string | number |null>()
const optionselect = ref<option | null>(null)
const filtered_options = ref<option[]>([])
const query = ref<string>('')
const isOpen = ref<boolean>(false)

const onFocus = () => {
  isOpen.value = true
}
const onMouseDown = (e: MouseEvent) => {
  if (isOpen.value) {
    // Si ya está abierto → cerrar
    e.preventDefault() // 👈 evita que vuelva a hacer focus
    isOpen.value = false
  } else {
    // Si está cerrado → abrir
    isOpen.value = true
  }
}
const onSelect = () => {
    query.value = ''
    if(props.close_when_selected){
        isOpen.value = false
    }
}
const handleScroll = () => {
  if (isOpen.value) {
    isOpen.value = false
  }
}

onMounted(() => {
  window.addEventListener('scroll', handleScroll, true) // 👈 CAPTURE
})

onBeforeUnmount(() => {
  window.removeEventListener('scroll', handleScroll, true)
})

watch([query, () => props.options], ([newQuery]) => {
  filtered_options.value = props.options.filter(option =>
    option.label.toLowerCase().includes(newQuery.toLowerCase())
  )
}, { immediate: true })

watch(optionselect, (val) => {
  selected.value = val ? val.value : null
})



watch([selected,() => props.options], () => {
  if (selected.value == null || selected.value === undefined) {
    optionselect.value = null
  } else {
    const found = props.options.find(o => o.value === selected.value)
    if (found) {
      optionselect.value = found
    }else if(props.options.length > 0){
      selected.value=null
      optionselect.value = null
    }
  }
}, { immediate: true })
const onInputChange=(event: Event)=> {
  if (props.searchable === false || props.disabled) {
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
  <ComboboxRoot :disabled="props.disabled" class="relative" v-model="optionselect" v-model:open="isOpen">
    <ComboboxAnchor :class="['inline-flex w-full relative border border-black rounded-md',{ inputfocusalways: isOpen }]">
      <ComboboxInput
        :class="['w-full ps-2 pr-8 truncate rounded border-none inputnotfocus']"
        @input="onInputChange"
        :readonly="props.searchable === false"
        :placeholder="placeholder"
        @focus="onFocus"
          @mousedown="onMouseDown"
        :displayValue="(option: option | null) => option?.label ?? ''"
      />
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
      class="z-15 w-[var(--reka-combobox-trigger-width)] bg-white border-2 border-gray-500 rounded-md">
      <ComboboxViewport>
        <ComboboxEmpty class="text-mauve8 text-xs font-medium text-center py-2">
         {{empty_message }}
        </ComboboxEmpty>
        <ComboboxGroup class="max-h-[25rem] overflow-auto">
          <ComboboxItem
            v-for="option in filtered_options"
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
    </ComboboxPortal>
  </ComboboxRoot>
  </div>
</template>
