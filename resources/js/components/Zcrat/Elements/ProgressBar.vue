<script setup lang="ts">
const props = defineProps<{
  width: number;
}>();

let percentage = '0%';

if (props.width > 100) {
  percentage = '100%';
} else if (props.width < 0) {
  percentage = '0%';
} else {
  percentage = `${props.width}%`;
}
</script>

<template>
  <div class="barra">
    <div :style="{ width: percentage }">
      <p>{{ percentage }}</p>
    </div>
  </div>
</template>
