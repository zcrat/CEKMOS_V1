<script setup lang="ts">
defineProps<{
  title:string
  order:'asc'|'desc'|null
}>()
</script>
<template>
    <p className="flex items-center justify-center gap-1 cursor-pointer">
        <span>{{title}}</span> 
        <font-awesome-icon 
            :icon="order == 'asc' 
                ? 'fa-solid fa-arrow-up-wide-short'
                :(order == 'desc' 
                    ? 'fa-solid fa-arrow-down-wide-short'
                    : 'fa-solid fa-sort'
                )" 
            :class="''"
        />   
    </p>
</template>