<script setup lang="ts">
import { computed, type Component } from 'vue';
import {
  DropdownMenuContent,
  DropdownMenuItem,
  DropdownMenuPortal,
  DropdownMenuRoot,
  DropdownMenuSeparator,
  DropdownMenuTrigger,
} from 'reka-ui'
import { now } from 'lodash';

const props = withDefaults(defineProps<{
  triggerText:string,
  align?:string,
  classtrigger?:{
    p?:string,
    border?:string,
    rounded?:string,
    bg?:string,
    text?:string,
    others?:string
  },
  width?:string,
  options:{
    label:string,
    onClick:(val?:any)=>void,
    classname:string[]
  }[],
  contentClasses?:{
    p?:string,
    border?:string,
    rounded?:string,
    bg?:string,
    text?:string,
    others?:string
  },
}>(),{
  align:'right',
  width:'w-fit',
});
const localTrigger = computed(() => ({
  p: "p-2",
  border: "border-2",
  rounded: "rounded-md",
  bg: "bg-[--micolor]",
  text: "text-white font-medium",
  ...(props.classtrigger ?? {})
}));

const localContent = computed(() => ({
  p: "py-2 px-3",
  border: "border-2 border-black",
  rounded: "rounded-md",
  bg: "bg-white",
  text: "text-black font-medium",
  ...(props.contentClasses ?? {})
}));

</script>

<template>

  <DropdownMenuRoot>
    <DropdownMenuTrigger :class="Object.values(localTrigger)">{{triggerText}}</DropdownMenuTrigger>
    <DropdownMenuPortal :class="[width]">
      <DropdownMenuContent :class="Object.values(localContent)">
        <template v-for="(child, index) in props.options" :key="index+'_'+now">
          <DropdownMenuItem :class="['text-center','py-1','text-black','text-md','hover:cursor-pointer',...child.classname]" @select="child.onClick">{{ child.label }}</DropdownMenuItem>
          <DropdownMenuSeparator />
        </template>
      </DropdownMenuContent>
    </DropdownMenuPortal>
  </DropdownMenuRoot>
</template>