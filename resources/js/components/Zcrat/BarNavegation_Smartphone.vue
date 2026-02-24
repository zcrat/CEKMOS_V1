<script setup lang="ts">
import NavLink from '@/components/NavLink.vue';
import Dropdown from '@/components/Dropdown.vue';
import DropdownLink from '@/components/DropdownLink.vue';
import { useAuth } from '@/composables/useAuth';
const { can,canAny } = useAuth();
import {router } from '@inertiajs/vue3';
import ButtonLink from './Inputs/ButtonLink.vue';
interface Props {
  ClassNav?: string
  barnav_active: boolean
}

const props = defineProps<Props>()

const logout = () => {
    router.post(route('logout'));
};

</script>
<template>
    <nav :class="['z-[2] border-b border-gray-100 w-[8rem] h-[calc(100vh-4.5rem)] top-[4.5rem]  absolute transform transition duration-300 ease-in-out',props.barnav_active ? 'translate-x-0 left-2':'-translate-x-full left-0'] ">
        <div :class="[
            'flex h-full bg-[#176cb3] rounded-md flex-col items-start justify-between max-w-full relative ',
             'sm:flex-col sm:me-2 sm:mb-0'
            ]">
        
        <div class=" flex-col flex-grow items-start justify-start gap-2 p-2 flex w-full">
            <NavLink :href="route('dashboard')" :active="route().current('dashboard')" class="ms-2 gap-x-1 w-full"><font-awesome-icon icon="fa-solid fa-house"/><span :class="'sm:hidden'">&nbsp;Inicio</span></NavLink>
            <NavLink v-if="can('ver_usuarios_sitema')" :href="route('users')" :active="route().current('users')" class="ms-2 gap-x-1 w-full">
                <font-awesome-icon icon="fa-solid fa-users"/>
                <span>&nbsp;Usuarios</span>
            </NavLink>
            <NavLink v-if="can('ver_empleados')" :href="route('employees')" :active="route().current('employees')" class="ms-2 gap-x-1 w-full">
                <font-awesome-icon icon="fa-solid fa-address-book "/>
                <span>&nbsp;Empleados</span>
            </NavLink>
            <Dropdown :align="'left-up'" width="48" classtrigger="w-full" classcontent="w-full" v-if="canAny(['ver_presupuestos','ver_ordenes_servicio'])">
                <template #trigger>
                    <ButtonLink 
                        :active="route().current('Cortana.Presupuesto.Vista') || route().current('Cortana.OrdenesServicio.Vista')"  
                        class="ms-2 gap-x-1 w-full text-white">
                        <font-awesome-icon icon="fa-solid fa-table-list" />
                        <span>&nbsp;Ordenes</span></ButtonLink>
                </template>
                
                <template #content>
                    <DropdownLink v-if="can('ver_presupuestos')" :href="route('Cortana.Presupuesto.Vista')">
                        Presupuestos
                    </DropdownLink>
                    <DropdownLink v-if="can('ver_ordenes_servicio')" :href="route('Cortana.OrdenesServicio.Vista')">
                        Recepciones Vehiculares
                    </DropdownLink>
                </template>
            </Dropdown>
            <Dropdown  
                v-if="canAny(['administrar_caja'])" 
                :align="'left-up'" 
                width="48" 
                classtrigger="w-full" 
                classcontent="w-full"
            >
                <template #trigger>
                    <ButtonLink 
                        :active="route().current('Admin.Caja') "  
                        class="ms-2 gap-x-1 w-full text-white"
                    >
                    <font-awesome-icon icon="fa-solid fa-user-gear " />
                    <span>&nbsp;Admin</span></ButtonLink>
                </template>
                
                <template #content>
                    <DropdownLink v-if="can('administrar_caja')" :href="route('Admin.Caja')">
                        Caja Movimientos
                    </DropdownLink>
                </template>
            </Dropdown>
        </div>
        <div class="ms-2 relative block p-2">
            <Dropdown :align="'left-up' " width="48">
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
                
                <div class="border-t border-gray-200"></div>
                
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