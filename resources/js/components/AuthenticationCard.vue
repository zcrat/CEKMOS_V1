<template>
    <div class="relative min-h-screen overflow-hidden flex justify-center items-center px-4 py-6 sm:py-0">
        <div
            aria-hidden="true"
            class="absolute inset-0 scale-110 bg-[url('/storage/Logos/Sistema/Fondo_Principal.png')] bg-cover bg-center bg-no-repeat blur-md"
        ></div>

        <div class="relative z-10 flex w-full max-w-[30rem] sm:h-[40rem] flex-col bg-[--color_logo] rounded-lg items-center justify-center p-2 border">
            <div class="rounded-lg overflow-hidden h-full">
                <slot name="logo" />
            </div>

            <div class="w-full">
                <slot />
            </div>
         </div>
    </div>
</template> 
