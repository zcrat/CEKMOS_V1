<script setup>
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    href: String,
    active: Boolean,
});

const classes = computed(() => {
    return 'inline-flex items-center px-1 pt-1 border-b-2 font-medium text-sm text-white transition duration-150 ease-in-out focus:outline-none focus:border-orange-300 leading-5 mx-2 '+(props.active
        ? 'border-orange-600'
        : 'border-transparent  hover:border-orange-300');
});
</script>

<template>
    <Link :href="href" :class="classes">
        <slot />
    </Link>
</template>
