<script setup lang="ts">
import { ref,onMounted,computed } from 'vue';
import { Head } from '@inertiajs/vue3';
import BarNavegation from '@/components/Zcrat/BarNavegation.vue';
import BarNavegation_Smartphone from '@/components/Zcrat/BarNavegation_Smartphone.vue';
import Loanding from '@/components/Zcrat/Elements/Loanding.vue';

const props = defineProps({
    title: String,
    loading: Boolean
});
const title=computed(() => props.title);
const loading=computed(() => props.loading);
const STORAGE_KEY:string = 'showingNavigationtop';
const isSmOrLarger = ref(false)
// Cargar valor inicial desde localStorage o usar true por defecto
const storedValue = localStorage.getItem(STORAGE_KEY);
const showingNavigationtop = ref<boolean>(
  storedValue !== null ? JSON.parse(storedValue) : true
);


const Barnav_smartphone_active = ref(false);
const checkWidth = () => {
  isSmOrLarger.value = window.matchMedia('(min-width: 640px)').matches
}

const toggleshowingNavigationtop = () => {
  showingNavigationtop.value = !showingNavigationtop.value
  localStorage.setItem(STORAGE_KEY, JSON.stringify(showingNavigationtop.value))
}

onMounted(() => {
  try {
    checkWidth()
    window.addEventListener('resize', checkWidth)
  } catch (error) {
    console.error('Error:', error)
  }
})

</script>

<template>
        <Head :title="title" />
        <div :class="['min-h-screen relative h-screen bg-gray-100 pt-2 px-2 flex flex-col overflow-auto',showingNavigationtop ? '' : 'sm:flex-row' ]">
            <BarNavegation :IsRow="showingNavigationtop" @toggle="toggleshowingNavigationtop()" @toggle_smartphone_active="Barnav_smartphone_active = !Barnav_smartphone_active"/>
            <BarNavegation_Smartphone v-if="!isSmOrLarger" :barnav_active="Barnav_smartphone_active"/>

            <main class="flex flex-col flex-1">
            <div class="flex flex-row gap-2 mt-2" v-if="$slots.header">
              <slot name="header"/>
            </div>
            <div class="flex-1 flex flex-col items-start justify-start">
              <div class="flex flex-col sm:flex-row justify-start w-full mt-2 gap-2" v-if="$slots.filtering">
                <slot name="filtering"/>
              </div>
              <div class="w-full flex-1 flex flex-col">
                <Loanding v-if="loading" text="Cargando Usuarios"/>
                <slot v-else name="content"/>
              </div>
            </div>
            <footer class="h-12 p-4 justify-center rounded-md bg-blue-100 border border-black my-2">
              <span class="flex flex-nowrap justify-between w-full text-xs">
                <div class="flex flex-row gap-2">
                  <strong>Copyright © 2025-2026</strong> Zcrat Developer. <p>All rights reserved.</p>
                </div>
                <strong>Version 1</strong>
              </span>
            </footer>
          </main>
        </div>
</template>
