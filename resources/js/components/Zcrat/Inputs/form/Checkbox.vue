<script setup lang="ts">
import { defineProps } from 'vue'
import { computed } from 'vue';
const emit = defineEmits(['update:checked']);
const props=defineProps<{
  classname?: string
  classlabel?: string
  classdiv?: string
  label?: string
  value: String
  checked:  Boolean
  id: string
  name: string

}>()

const proxyChecked = computed({
    get() {
        return props.checked;
    },
    set(val) {
        emit('update:checked', val);
    },
});

</script>
<template>
  <div :class="['flex  flex-row justify-start items-center gap-x-2 p-2', classdiv]">
      <input
        :id="id"
        :name="name"
        v-model="proxyChecked"
        type="checkbox"
        :value="value"
        :class="classname? classname :'rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500'"
      >
      <label v-if="props.label" :class="classlabel??''" :for="id" >{{props.label}}</label>
    </div>
</template>