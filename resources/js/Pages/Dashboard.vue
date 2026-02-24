<script setup>
import LogoInicio from '@/components/Zcrat/LogoInicio.vue';
import AppLayout from '@/layouts/AppLayout.vue';
</script>

<template>
    <AppLayout title="Pagina Principal">
        <template #content>
            
            <div class="h-full flex flex-col items-center justify-center">
                <h1 class="font-black text-[3rem] text-center mb-4 leading-10 sm:mb-20">Bienvenido Al Nuevo Sistema De KARWORKS</h1>
                <LogoInicio className="h-[calc(50vh-2rem)]  mx-auto mb-4" />
            </div>
        </template>
    </AppLayout>
</template>
