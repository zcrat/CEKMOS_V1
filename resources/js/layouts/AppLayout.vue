<script setup>
import { ref,watch,onMounted } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import Topbar from '@/components/Zcrat/Topbar.vue';
import BarNavegation from '@/components/Zcrat/BarNavegation.vue';
import BarNavegation_Smartphone from '@/components/Zcrat/BarNavegation_Smartphone.vue';

defineProps({
    title: String,
});

const STORAGE_KEY = 'showingNavigationtop';
const isSmOrLarger = ref(false)
// Cargar valor inicial desde localStorage o usar true por defecto
const showingNavigationtop = ref(
  localStorage.getItem(STORAGE_KEY) === null
    ? true
    : JSON.parse(localStorage.getItem(STORAGE_KEY))
);

const Barnav_smartphone_active = ref(false);
const checkWidth = () => {
  isSmOrLarger.value = window.matchMedia('(min-width: 640px)').matches
}
const toggleshowingNavigationtop = ()=>{
   showingNavigationtop.value = !showingNavigationtop.value
localStorage.setItem(STORAGE_KEY, showingNavigationtop.value)
}
onMounted(() => {
  checkWidth()
  window.addEventListener('resize', checkWidth)
})
</script>

<template>
    <div>
        <Head :title="title" />
        <div :class="['min-h-screen relative h-screen bg-gray-100 pt-2 px-2 flex flex-col',showingNavigationtop ? '' : 'sm:flex-row']">
            <BarNavegation :IsRow="showingNavigationtop" @toggle="toggleshowingNavigationtop()" @toggle_smartphone_active="Barnav_smartphone_active = !Barnav_smartphone_active"/>
            <BarNavegation_Smartphone v-if="!isSmOrLarger" :barnav_active="Barnav_smartphone_active"/>

            <main class="h-full w-full">
                <slot />
            </main>
        </div>
    </div>
</template>
