<script setup>
defineProps({
    type: {
        type: String,
        default: 'submit',
    },
});
</script>

<template>
    <button :type="type" class="p-2 rounded-md bg-[#c0392b] text-white font-semibold uppercase">
        <slot />
    </button>
</template>
