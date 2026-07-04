<template>
    <div class="min-h-screen flex justify-center items-center pt-6 sm:pt-0 bg-[url(/storage/Logos/Sistema/Fondo_Principal.png)] bg-no-repeat bg-cover ">
        <div class="flex w-[30rem] sm:h-[40rem] h-full flex-col bg-[--color_logo] rounded-lg items-center justify-center p-2 relative border">
            <div class="rounded-lg overflow-hidden h-full">
                <slot name="logo" />
            </div>

            <div class="w-full">
                <slot />
            </div>
         </div>
    </div>
</template> 
