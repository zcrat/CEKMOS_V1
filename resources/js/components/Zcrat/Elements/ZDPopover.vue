<template>
  <PopoverRoot>
    <PopoverTrigger as-child>
      <button
        class="group inline-flex items-center rounded-md border border-black px-3 py-2 text-base font-medium focus:outline-none focus-visible:ring-2 focus-visible:ring-white/75"
      >
        <span>{{ props.father }}</span> 
        <ChevronDownIcon
          :class=" ''"
          class="ml-2 h-5 w-5 transition duration-150 ease-in-out group-hover:text-gray-500"
          aria-hidden="true"
        />
      </button>
    </PopoverTrigger>
    <PopoverPortal>
      <PopoverContent
        side="bottom"
        align="start"
        :side-offset="8"
        :avoid-collisions="true"
        :collision-padding="8"
        :class="['z-[55]', classname ?? '', 'flex w-[15rem] flex-col overflow-y-auto rounded border border-gray-500 bg-white p-2 shadow-lg sm:max-h-[40vh] max-h-[60vh]']"
      >
        <component
        v-for="(child, index) in props.children"
        :key="index"
        :is="child.element"
        v-bind="child.props || {}"
        />
      </PopoverContent>
    </PopoverPortal>
  </PopoverRoot>
</template>

<script setup lang="ts">
  import { PopoverContent, PopoverPortal, PopoverRoot, PopoverTrigger } from 'reka-ui'
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
