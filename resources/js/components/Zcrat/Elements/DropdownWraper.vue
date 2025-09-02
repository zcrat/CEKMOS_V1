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
  stoppropagation?:boolean
}>();
function handleClick(event: MouseEvent) {
  if (props.stoppropagation) {
    event.stopPropagation();
  }
}

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
      <div @click="handleClick" class="p-2 gap-2 flex flex-col ">
        <component
        v-for="(child, index) in props.children"
        :key="index"
        :is="child.element"
        v-bind="child.props || {}"
        />
      </div>
    </template>
  </Dropdown>
</template>