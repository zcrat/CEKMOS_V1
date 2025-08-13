<script setup>
import { computed } from 'vue';
const props = defineProps({
    label: String,
    active: Boolean,
});
const classes = computed(() => {
    return 'inline-flex items-center  border-b-2 font-medium text-sm text-white transition duration-150 ease-in-out focus:outline-none focus:border-orange-300 leading-5 '+(props.active
        ? 'border-orange-600'
        : 'border-transparent  hover:border-orange-300');
});
</script>

<template>
     <span :class="classes">
        <button type="button" class="p-0 border-none m-0">
           <slot />
        </button>
    </span>
</template>