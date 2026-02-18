<template>
    <div class="min-h-screen flex justify-center items-center pt-6 sm:pt-0 bg-[url(/storage/Logos/Sistema/Fondo_Principal.png)] bg-no-repeat bg-cover ">
        <div class="flex w-full sm:w-[40rem] sm:h-[40rem] flex-col rounded-full bg-white  bg-tr items-center justify-center overflow-hidden">
        <div class="sm:w-[30rem] w-[10rem] sm:p-0 pt-4">
            <slot name="logo" />
        </div>

        <div class="sm:w-[30rem] w-[18rem] sm:mt-6  bg-white overflow-hidden sm:rounded-lg">
            <slot />
        </div>
         </div>
    </div>
</template> 
