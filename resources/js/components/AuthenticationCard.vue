<template>
    <div class="min-h-screen flex justify-center items-center pt-6 sm:pt-0 bg-[url(/storage/Logos/Sistema/Fondo_Principal.png)] bg-no-repeat bg-cover ">
        <div class="flex w-[40rem] h-[40rem] flex-col rounded-full bg-white  bg-tr items-center justify-center overflow-hidden">
        <div class="w-[30rem] p-0">
            <slot name="logo" />
        </div>

        <div class="w-[30rem] mt-6 bg-white overflow-hidden sm:rounded-lg">
            <slot />
        </div>
         </div>
    </div>
</template> 
