<script setup>
defineProps({
    Classdiv: {
    type: String,
    default: '', 
  },
    Classinput: {
    type: String,
    default: '', 
  }
});
</script>
<template>
     <div :class="['relative flex',Classdiv]">
        <input type="search" name="search" id="search" :class="['rounded-lg w-full pe-8 shadoew-none focus:shadow-[0px_0px_0.7rem] focus:shadow-red-700 focus:ring-red-700 focus:border-red-700',Classinput]">
        <font-awesome-icon icon="fa-solid fa-search " class="absolute end-0 p-1 top-[50%] transform -translate-y-[50%] text-[1.25em] mx-2"/>
    </div>
</template>