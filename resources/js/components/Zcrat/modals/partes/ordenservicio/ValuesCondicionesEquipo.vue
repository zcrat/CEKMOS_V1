<script setup lang="ts">
  import { OpcionesCondicionesEquipo } from '@/utils/variables/options';
</script>
<template>
      <div class="border rounded-md py-2 my-2 flex gap-2 justify-around flex-wrap">
      <div v-for="(item,index) in OpcionesCondicionesEquipo" :key="'leyenda-'+index" class="flex items-center gap-1">
        <font-awesome-icon :icon="item.icon" v-if="item.icon"></font-awesome-icon><p v-if="item.value">{{ item.value }}</p>=<p>{{item.label}}</p>
      </div>
    </div>
</template>