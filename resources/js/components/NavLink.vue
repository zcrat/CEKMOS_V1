<script setup>
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    href: String,
    title: String,
    active: Boolean,
});

const classes = computed(() => {
    return 'inline-flex items-center  border-b-2 font-medium text-sm text-white transition duration-150 ease-in-out focus:outline-none focus:border-orange-300 leading-5 '+(props.active
        ? 'border-orange-600'
        : 'border-transparent  hover:border-orange-300');
});
</script>

<template>
    <Link :href="href" :class="classes" :title="title ?? ''">
        <slot />
    </Link>
</template>
