<script setup>
defineProps({
    ClassName: {
    type: String,
    default: 'h-10', 
  }
});
</script>
<template>
     <img src="/storage/Logos/Sistema/Logo_CEKMOS.png" :class="ClassName"/>
</template>