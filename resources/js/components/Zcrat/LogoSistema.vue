<script setup>
defineProps({
    ClassName: {
    type: String,
    default: 'h-10', 
  }
});
</script>
<template>
     <img src="/storage/Logos/Sistema/logo.png" :class="ClassName"/>
</template>