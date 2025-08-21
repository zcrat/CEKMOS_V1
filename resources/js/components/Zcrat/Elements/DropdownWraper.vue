<script setup lang="ts">
import { computed, type Component } from 'vue';
import Dropdown from '@/components/Dropdown.vue';

interface ComponentInterface {
  element: Component;
  props?: Record<string, any>;
}

const props = defineProps<{
  father: ComponentInterface;
  children: ComponentInterface[];
  align?:string;
  width?:string;
}>();
</script>

<template>
  <Dropdown :align="align || 'center'" :width="width ?? '48'">
    <template #trigger>
      <component
        :is="props.father.element"
        v-bind="props.father.props || {}"
      />
    </template>

    <template #content>
      <component
        v-for="(child, index) in props.children"
        :key="index"
        :is="child.element"
        v-bind="child.props || {}"
      />
    </template>
  </Dropdown>
</template>