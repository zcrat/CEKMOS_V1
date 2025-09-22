<script setup>
import LogoInicio from '@/components/Zcrat/LogoInicio.vue';
import AppLayout from '@/Layouts/AppLayout.vue';
</script>

<template>
    <AppLayout title="Pagina Principal">
        <template #header>
        </template>

        <div class="h-full flex flex-col items-center justify-center">
            <h1 class="font-black text-[3rem] text-center mb-4 leading-10 mb-20">Bienvenido Al Nuevo Sistema De Akumas</h1>
            <LogoInicio className="h-[calc(50vh-2rem)]  mx-auto mb-4" />
        </div>
    </AppLayout>
</template>
