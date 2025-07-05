<script setup>
import { ref,watch } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import Topbar from '@/components/Zcrat/Topbar.vue';
import BarNavegation from '@/components/Zcrat/BarNavegation.vue';

defineProps({
    title: String,
});

const STORAGE_KEY = 'showingNavigationtop';

// Cargar valor inicial desde localStorage o usar true por defecto
const showingNavigationtop = ref(
  localStorage.getItem(STORAGE_KEY) === null
    ? true
    : JSON.parse(localStorage.getItem(STORAGE_KEY))
);

// Observar cambios y guardarlos en localStorage
watch(showingNavigationtop, (newVal) => {
    console.log('showingNavigationtop changed:', newVal);
  localStorage.setItem(STORAGE_KEY, JSON.stringify(newVal));
});

</script>

<template>
    <div>
        <Head :title="title" />
        <div :class="['min-h-screen h-screen bg-gray-100 pt-2 px-2 flex flex-col',showingNavigationtop ? '' : 'sm:flex-row']">
            <BarNavegation :IsRow="showingNavigationtop" @toggle="showingNavigationtop = !showingNavigationtop"/>
            <main class="h-full w-full">
                <slot />
            </main>
        </div>
    </div>
</template>
