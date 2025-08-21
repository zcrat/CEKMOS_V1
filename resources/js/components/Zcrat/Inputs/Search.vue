<script setup lang="ts">
const props = withDefaults(defineProps<{Classdiv?: string;Classinput?: string}>(),{
    Classdiv: '', 
    Classinput: '', 
});
</script>
<template>
     <div :class="['relative flex',props.Classdiv]">
        <input type="search" name="search" id="search" :class="['rounded-lg w-full pe-8 focus-input',props.Classinput]">
        <font-awesome-icon icon="fa-solid fa-search " class="absolute end-0 p-1 top-[50%] transform -translate-y-[50%] text-[1.25em] mx-2"/>
    </div>
</template>