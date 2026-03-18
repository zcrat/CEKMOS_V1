<script lang="ts" setup>
    withDefaults(defineProps<{
        bg?:'bg-[--micolor]'|'bg-[--color4]'
    }>(), {
        bg: 'bg-[--micolor]' ,
    });
</script>
<template>
    <h2 :class="'w-full text-center text-md font-bold p-2 my-2 rounded-md text-white '+ bg"><slot></slot></h2>
</template>