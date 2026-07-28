<script setup lang="ts">
import ZDIconError from '@/components/Zcrat/Elements/ZDIconError.vue';
import ZDListErrors from '@/components/Zcrat/Elements/ZDListErrors.vue';
import { watch } from 'vue';

const modelValue = defineModel<string | number |null>()
const props=defineProps<{
  id:string
  classname?: string
  classdiv?: string
  placeholder?: string
  label?: string
  errors?: string[]
  DeleteErrors?: () => void
}>()

watch(modelValue, () => props.DeleteErrors?.());
</script>
<template>
  <div :class="['flex flex-col justify-start relative', classdiv]">
    <label v-if="props.label">
      <ZDIconError :errors="props.errors" hidden-absolute />
      {{props.label}}
    </label>
    <textarea
      v-model="modelValue"
      :id="id"
      :name="id"
      :placeholder="props.placeholder"
      :class="[
        'w-full rounded-md inputfocus',
        props.classname,
        props.errors?.length ? 'inputerror' : '',
      ]"
    />
    <ZDListErrors :errors="props.errors" />
  </div>
</template>
