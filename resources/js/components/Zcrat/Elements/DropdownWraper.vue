<script setup lang="ts">
import { computed, type Component } from 'vue';
import Dropdown from '@/components/Zcrat/Elements/Dropdown.vue';
import {
  DropdownMenuItem,
} from 'reka-ui'
interface ComponentInterface {
  element: DropdownMenuItem;
  props?: Record<string, any>;
}
interface Item {
  label: string;
  props?: Record<string, any>;
}

const props = defineProps<{
  children: Item[];
  father: Item;
  align?:string;
  width?:string;
  stoppropagation?:boolean;
  contentClasses?:string[];
  classcontent?:string;
}>();

</script>

<template>
  <Dropdown 
  :triggerText="father.label"
  :align="align || 'center'" :width="width ?? '48' " :contentClasses="contentClasses" :classcontent="classcontent">

      <!-- <template #trigger>
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
    </template> -->
  </Dropdown>
</template>