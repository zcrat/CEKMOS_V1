<template>
  <Popover class="relative">
    <PopoverButton
        :class="''"
        class="group inline-flex items-center rounded-md border border-black px-3 py-2 text-base font-medium focus:outline-none focus-visible:ring-2 focus-visible:ring-white/75"
      >
        <span>{{ props.father }}</span> 
        <ChevronDownIcon
          :class=" ''"
          class="ml-2 h-5 w-5 transition duration-150 ease-in-out group-hover:text-gray-500"
          aria-hidden="true"
        />
      </PopoverButton>
    <PopoverPanel class="absolute z-10 mt-2 ZDLeft">
      <div class="p-2  flex flex-col w-[15rem] bg-white rounded border border-gray-500 shadow-lg" :class="classname??''">
        <component
        v-for="(child, index) in props.children"
        :key="index"
        :is="child.element"
        v-bind="child.props || {}"
        />
    </div>
    </PopoverPanel>
  </Popover>
</template>

<script setup lang="ts">
  import { Popover, PopoverButton, PopoverPanel } from '@headlessui/vue'
  import { ChevronDownIcon } from '@heroicons/vue/20/solid'
  import { computed, type Component } from 'vue';
  interface ComponentInterface {
    element: Component;
    props?: Record<string, any>;
    }
  const props = defineProps<{
    classname?: string
    children: ComponentInterface[];
    father:string
  }>()
</script>