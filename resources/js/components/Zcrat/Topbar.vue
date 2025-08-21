<script setup>
import NavLink from '@/components/NavLink.vue';
import Dropdown from '@/components/Dropdown.vue';
import DropdownLink from '@/components/DropdownLink.vue';
import LogoSistema from '@/components/Zcrat/LogoSistema.vue';
defineProps({
    ClassNav: {
    type: String,
    default: '', 
  }
});
</script>
<template>
    
    <nav :class="['bg-white border-b border-gray-100', ClassNav]">
        <div class="flex h-full bg-[#176cb3] rounded-md items-center justify-center sm:justify-between max-w-full relative">
        <LogoSistema ClassName="h-12 pl-2" />
        
        <div class=" flex-row flex-grow items-start hidden sm:flex">
            <NavLink :href="route('dashboard')" :active="route().current('dashboard')"><font-awesome-icon icon="fa-solid fa-house"/>&nbsp;Inicio</NavLink>
        </div>
        <div class="ms-3 relative hidden sm:block">
            <Dropdown align="right" width="48">
            <template #trigger>
                    <span class="inline-flex rounded-md mr-2">
                        <button type="button" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none focus:bg-gray-50 active:bg-gray-50 transition ease-in-out duration-150">
                            {{ $page.props.auth.user.name }}
                            <font-awesome-icon icon="fa-solid fa-gear"  class="ms-2"/>
                    </button>
                </span>
            </template>
            
            <template #content>
                <!-- Account Management -->
                <div class="block px-4 py-2 text-xs text-gray-400">
                    Configuraciones
                </div>
                
                <DropdownLink :href="route('profile.show')">
                    Mi Perfil
                </DropdownLink>
                
                <div class="border-t border-gray-200" />
                
                <!-- Authentication -->
                <form @submit.prevent="logout">
                    <DropdownLink as="button">
                        Cerrar Sesión
                    </DropdownLink>
                </form>
            </template>
        </Dropdown>
        </div>
        <font-awesome-icon icon="fa-solid fa-bars"  class="absolute right-5 text-lg font-bold text-white sm:hidden"/>
        </div>
    </nav>
</template>