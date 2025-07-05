<script setup>
import { ref } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import Topbar from '@/components/Zcrat/Topbar.vue';
import BarNavegation from '@/components/Zcrat/BarNavegation.vue';

defineProps({
    title: String,
});

const showingNavigationtop = ref(true);;


</script>

<template>
    <div>
        <Head :title="title" />
        <div :class="['min-h-screen h-screen bg-gray-100 pt-2 px-2 flex',showingNavigationtop ? 'flex-row' : 'flex-col']">
            <BarNavegation :IsRow="showingNavigationtop" @toggle="showingNavigationtop = !showingNavigationtop"/>
            <main class="h-full w-full">
                <slot />
            </main>
        </div>
    </div>
</template>
