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
const emit = defineEmits(['toggle','toggle_smartphone_active']);
function toggleNav() {
  emit('toggle');
}
function toggleNavsmartphone() {
    emit('toggle_smartphone_active');
}
</script>
<template>
    <nav :class="['border-b border-gray-100 w-full h-[4rem]', ClassNav, IsRow ?'':'sm:h-full sm:w-[5rem]' ]">
        <div :class="[
            'flex h-full bg-[#176cb3] rounded-md flex-row items-center mb-2 justify-center sm:justify-between max-w-full relative ',
            IsRow ? '' : 'sm:flex-col sm:me-2 sm:mb-0'
            ]">

            <div>

                <LogoSistema :ClassName="'h-12 flex-grow-0 '+(IsRow?'mx-2':'sm:my-2')"  @click="toggleNav"/>
            </div>
        
        <div :class="'flex-grow justify-start gap-x-2 gap-y-4 hidden sm:flex '+(IsRow?'flex-row':'flex-col')">
            <NavLink :href="route('dashboard')" :active="route().current('dashboard')"><font-awesome-icon icon="fa-solid fa-house" :class="IsRow?'':'sm:text-[1.3rem]'"/><span :class="IsRow?'':'sm:hidden'">&nbsp;Inicio</span></NavLink>
            <NavLink :href="route('users')" :active="route().current('users')"><font-awesome-icon icon="fa-solid fa-users ":class="IsRow?'':'sm:text-[1.3rem]'"/><span :class="IsRow?'':'sm:hidden'">&nbsp;Usuarios</span></NavLink>
            <NavLink :href="route('employees')" :active="route().current('employees')"><font-awesome-icon icon="fa-solid fa-address-book ":class="IsRow?'':'sm:text-[1.3rem]'"/><span :class="IsRow?'':'sm:hidden'">&nbsp;Empleados</span></NavLink>
        </div>
        <div class="ms-3 relative hidden sm:block">
            <Dropdown :align="IsRow?'right':'left-up' " width="48">
            <template #trigger>
                    <span class="inline-flex rounded-md mr-2">
                        <button type="button" :class="IsRow?'inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none focus:bg-gray-50 active:bg-gray-50 transition ease-in-out duration-150':''">
                            <template v-if="IsRow">
                                {{ $page.props.auth.user.name }}
                            </template>
                            <font-awesome-icon icon="fa-solid fa-gear" :class="IsRow?'ms-2':'sm:text-[1.5rem] mb-2 text-white'"/>
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
        <font-awesome-icon icon="fa-solid fa-bars" @click="toggleNavsmartphone" class="absolute right-5 text-lg font-bold text-white sm:hidden"/>
        </div>
    </nav>
</template>