<script setup lang="ts">
import LogoSistema from '@/components/Zcrat/LogoSistema.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps<{
    path: string;
    authenticated: boolean;
}>();

const destination = computed(() => route(props.authenticated ? 'dashboard' : 'login'));
const destinationLabel = computed(() => (props.authenticated ? 'Volver al inicio' : 'Ir a iniciar sesión'));

const goBack = () => {
    if (window.history.length > 1) {
        window.history.back();
        return;
    }

    router.visit(destination.value);
};
</script>

<template>
    <Head title="Acceso denegado" />

    <main class="flex min-h-screen items-center justify-center bg-gray-100 px-6 py-12">
        <section class="w-full max-w-2xl overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-xl">
            <div class="h-2 bg-[#b30e32]"></div>

            <div class="flex flex-col items-center px-6 py-12 text-center sm:px-12">
                <LogoSistema ClassName="mb-8 h-16 max-w-full object-contain" />
                
                <h1 class="mt-3 text-3xl font-black text-gray-900 sm:text-4xl">Acceso denegado</h1>
                <p class="mt-4 max-w-lg text-base leading-7 text-gray-600">
                    No tienes el permiso necesario para consultar esta página. Si necesitas acceso, solicítalo a un administrador.
                </p>

                <code class="mt-6 max-w-full overflow-hidden text-ellipsis whitespace-nowrap rounded-md bg-gray-100 px-4 py-2 text-sm text-gray-600">
                    {{ path }}
                </code>

                <div class="mt-8 flex flex-wrap justify-center gap-3">
                    <Link
                        :href="destination"
                        class="rounded-md bg-[#176cb3] px-5 py-3 font-semibold text-white transition hover:bg-blue-800"
                    >
                        {{ destinationLabel }}
                    </Link>
                    <button
                        type="button"
                        class="rounded-md border border-gray-300 bg-white px-5 py-3 font-semibold text-gray-700 transition hover:bg-gray-100"
                        @click="goBack"
                    >
                        Regresar
                    </button>
                </div>
            </div>
        </section>
    </main>
</template>
