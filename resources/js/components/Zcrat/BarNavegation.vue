<script setup lang="ts">
import NavLink from '@/components/NavLink.vue';
import Dropdown from '@/components/Dropdown.vue';
import DropdownLink from '@/components/DropdownLink.vue';
import ButtonLink from '@/components/Zcrat/Inputs/ButtonLink.vue';
import LogoSistema from '@/components/Zcrat/LogoSistema.vue';
import Notification from '@/components/Zcrat/Notification.vue';
import { defineEmits } from 'vue';

interface Props {
  ClassNav?: string;
  IsRow: boolean;
}

const props = defineProps<Props>();

const emit = defineEmits(['toggle','toggle_smartphone_active']);
function toggleNav() {
  emit('toggle');
}
function toggleNavsmartphone() {
    emit('toggle_smartphone_active');
}
</script>
<template>
    <nav :class="['border-b border-gray-100 w-full h-[4rem]', props.ClassNav, IsRow ?'':'sm:h-full sm:w-[5rem]' ]">
        <div :class="[
            'flex h-full bg-[#176cb3] rounded-md flex-row items-center mb-2 justify-center sm:justify-between max-w-full relative ',
            IsRow ? '' : 'sm:flex-col sm:me-2 sm:mb-0'
            ]">

            <div>

                <LogoSistema :ClassName="'h-12 flex-grow-0 '+(IsRow?'mx-2':'sm:my-2')"  @click="toggleNav"/>
            </div>
        
        <div :class="'flex-grow justify-start gap-x-2 gap-y-4 hidden sm:flex '+(IsRow?'flex-row':'flex-col')">
            <NavLink :href="route('dashboard')" :active="route().current('dashboard')"><font-awesome-icon icon="fa-solid fa-house" :class="IsRow?'':'sm:text-[1.3rem]'"/><span :class="IsRow?'':'sm:hidden'">&nbsp;Inicio</span></NavLink>
            <NavLink :href="route('users')" :active="route().current('users')"><font-awesome-icon icon="fa-solid fa-users " :class="IsRow?'':'sm:text-[1.3rem]'"/><span :class="IsRow?'':'sm:hidden'">&nbsp;Usuarios</span></NavLink>
            <NavLink :href="route('employees')" :active="route().current('employees')"><font-awesome-icon icon="fa-solid fa-address-book " :class="IsRow?'':'sm:text-[1.3rem]'"/><span :class="IsRow?'':'sm:hidden'">&nbsp;Empleados</span></NavLink>
                <Dropdown :align="IsRow?'right':'left-up' " width="48">
                    <template #trigger>
                        <ButtonLink :active="route().current('Cortana.Presupuesto.Vista')"><font-awesome-icon icon="fa-solid fa-address-book " :class="IsRow?'':'sm:text-[1.3rem]'"/><span :class="IsRow?'':'sm:hidden'">&nbsp;Cortana</span></ButtonLink>
                    </template>
                    
                    <template #content>
                        <DropdownLink :href="route('Cortana.Presupuesto.Vista',{'modulo':'CFE'})">
                            CFE
                        </DropdownLink>
                        <DropdownLink :href="route('Cortana.Presupuesto.Vista',{'modulo':'CFB'})">
                            CFB
                        </DropdownLink>
                        <DropdownLink :href="route('Cortana.Presupuesto.Vista',{'modulo':'ECO'})">
                            ECO
                        </DropdownLink>
                    </template>
                </Dropdown>
        </div>
        <Notification :classname="IsRow?'sm:ms-2':'sm:mb-2'" :IsRow="IsRow"></Notification>
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
                <form>
                    <DropdownLink as="button">
                        Cerrar Sesión
                    </DropdownLink>
                </form>
            </template>
            </Dropdown>
        </div>
        <font-awesome-icon icon="fa-solid fa-bars" @click="toggleNavsmartphone" class="absolute left-5 text-lg font-bold text-white sm:hidden"/>
        </div>
    </nav>
</template>