<script setup>
import NavLink from '@/Components/NavLink.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import LogoSistema from '@/components/Zcrat/LogoSistema.vue';
import { defineEmits } from 'vue';

const props = defineProps({
    ClassNav: {
    type: String,
    default: '', 
  },
    barnav_active: {
        type: Boolean,
        required: true, 
    }
});


</script>
<template>
    <nav :class="['border-b border-gray-100 w-[8rem] h-[calc(100vh-4.5rem)] top-[4.5rem]  absolute transform transition duration-300 ease-in-out',barnav_active ? 'translate-x-0 left-2':'-translate-x-full left-0'] ">
        <div :class="[
            'flex h-full bg-[#176cb3] rounded-md flex-col items-start justify-between max-w-full relative ',
            IsRow ? 'mb-2' : 'sm:flex-col sm:me-2 sm:mb-0'
            ]">
        
        <div class=" flex-col flex-grow items-start justify-start gap-2 p-2 flex">
            <NavLink :href="route('dashboard')" :active="route().current('dashboard')" class="ms-2 gap-x-1 w-full"><font-awesome-icon icon="fa-solid fa-house"/><span :class="IsRow?'':'sm:hidden'">&nbsp;Inicio</span></NavLink>
            <NavLink :href="route('users')" :active="route().current('users')" class="ms-2 gap-x-1 w-full"><font-awesome-icon icon="fa-solid fa-users"/><span :class="IsRow?'':'sm:hidden'">&nbsp;Usuarios</span></NavLink>
        </div>
        <div class="ms-2 relative block p-2">
            <Dropdown :align="IsRow?'right':'left-up' " width="48">
            <template #trigger>
                    <span class="inline-flex rounded-md mr-2">
                        <button type="button" class="'inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none focus:bg-gray-50 active:bg-gray-50 transition ease-in-out duration-150">
                            <font-awesome-icon icon="fa-solid fa-gear" class="sm:text-[1.5rem] "/>
                            {{ $page.props.auth.user.name }}
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
       
        </div>
    </nav>
</template>