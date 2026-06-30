<script setup lang="ts">
    const props = withDefaults(defineProps<{Classdiv?: string;Classinput?: string,placeholder?:string}>(),{
        Classdiv: '', 
        Classinput: '',
    });
    const search = defineModel<string>({ default: '' });

    const clearSearch = () => {
        search.value = '';
    };
</script>
<template>
     <div :class="['relative flex',props.Classdiv]">
        <input type="text" name="search" id="search" v-model="search"  :class="['rounded-lg w-full pe-[2.2rem] inputfocus',props.Classinput] " :placeholder="props.placeholder || 'Buscar...'"/>
        <button
            v-if="search"
            type="button"
            aria-label="Limpiar busqueda"
            class="absolute end-0 top-[50%] mx-2 flex h-7 w-7 -translate-y-[50%] items-center justify-center"
            @mousedown.prevent
            @click="clearSearch"
        >
            <font-awesome-icon icon="fa-solid fa-x" class="text-[0.9rem]"/>
        </button>
        <font-awesome-icon v-else icon="fa-solid fa-search " class="pointer-events-none absolute end-0 p-1 top-[50%] transform -translate-y-[50%] text-[1.25em] mx-2"/>
    </div>
</template>