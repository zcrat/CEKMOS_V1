<script setup>
defineProps({
    ClassName: {
    type: String,
    default: 'h-10', 
  }
});
</script>
<template>
     <img src="/storage/logos/sistema/karworks.png" :class="ClassName"/>
</template>