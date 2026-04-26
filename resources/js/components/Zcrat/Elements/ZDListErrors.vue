<script setup lang="ts">
const props = defineProps<{
  errors?: string[];
}>();
</script>

<template>
    <div v-if="props.errors">
      <h4  v-for="(value,index) in props.errors"  :key="index" class="text-red-500 font-light capitalize italic ">{{ value }}</h4>
    </div>
</template>