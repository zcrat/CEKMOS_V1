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
    IsRow: {
        type: Boolean,
        required: true, 
    }
});
const emit = defineEmits(['toggle']);
function toggleNav() {
    console.log('Toggle Navigation');
  emit('toggle');
}

</script>
<template>
    <nav :class="['bg-white border-b border-gray-100 w-full h-[4rem]', ClassNav, IsRow ?'':'sm:h-full sm:w-[5rem]' ]">
        <div :class="[
            'flex h-full bg-[#176cb3] rounded-md flex-row items-center justify-center sm:justify-between max-w-full relative ',
            IsRow ? 'mb-2' : 'sm:flex-col sm:me-2 sm:mb-0'
            ]">

            <div>

                <LogoSistema ClassName="h-12 pl-2 flex-grow-0"  @click="toggleNav"/>
            </div>
        
        <div class=" flex-wrap flex-grow items-start hidden sm:flex">
            <NavLink :href="route('dashboard')" :active="route().current('dashboard')"><font-awesome-icon icon="fa-solid fa-house" :class="IsRow?'':'sm:text-[1.3rem]'"/><span :class="IsRow?'':'sm:hidden'">&nbsp;Inicio</span></NavLink>
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