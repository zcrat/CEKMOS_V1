<script setup lang="ts">
import Dropdown from '@/components/Dropdown.vue';
import Button from '@/components/Zcrat/Inputs/Button.vue';
import { ref,onMounted} from 'vue'

import { useEcho } from '@laravel/echo-vue';
import axios from 'axios' 

interface Props {
    classname?:string
    IsRow:boolean
    IdUser:number
}
const props = defineProps<Props>();
interface Notification {
    id: number;
    type: string;
    body: string;
    title: string;
    created_at: string;
    read: boolean;
    show?: boolean ;
}
import { Ref } from 'vue';

const notificaciones: Ref<Notification[]> = ref([]);
const countnotificaciones = ref(0);
const shownotifications = ref(5);
const hasmore = ref(false);


const getnotificaciones = async () => {
    try {
        const response = await axios.get(route('getnotifications'),{
            params: {
                shownotifications: shownotifications.value,
            },
        });
        notificaciones.value=response.data.notificaciones ? 
        response.data.notificaciones.map((n: any) => ({
        ...n,
        show: false
        })):[];
        countnotificaciones.value=response.data.countnotificaciones;
        hasmore.value=response.data.hasmore;
    } catch (error : any) {
        console.error('Error:', error)
    }
};
const readnotificacion = async (id:number) => {
    try {
        axios.get(route('readnotification'),{
            params: {
                id: id,
            },
        });
    } catch (error : any) {
        console.error('Error:', error)
    }
};
getnotificaciones();
</script>
<template>
    <div class="absolute right-5 sm:static">
     <Dropdown :align="IsRow?'noti-right':'noti-left-up'" width="auto" :contentClasses="['bg-white', 'overflow-hidden p-2 border border-gray-600']">
        <template #trigger>
            <div class="relative cursor-pointer">
                <Button icon="fa-solid fa-bell" text="" :hiddenclases="true" :classname="'text-[#eab308] text-[1.5rem] '+(props.classname||'')" @click="()=>{
                    notificaciones.forEach(n=>n.show=false);
                    shownotifications=5;
                }"/>
                <span v-if="countnotificaciones>0" class="text-[0.5rem] flex p-2 rounded-full size-4 justify-center items-center bg-red-600 font-bold absolute start-[80%] bottom-[60%]"> {{ countnotificaciones > 99 ? '99+' : countnotificaciones }}</span>
            </div>
        </template>
        
        <template #content >
        <div class="max-h-[30rem] w-[calc(100vw-2rem)] sm:w-[20rem] overflow-y-auto gap-2 flex flex-col" @click.stop>
            <div v-if="notificaciones.length ===0" class="text-center w-full" >
            <h2 class="font-bold text-[1rem]">No hay notificaciones</h2>
            <p class="text-sm">Cuando tengas nuevas notificaciones, aparecerán aquí.</p >
            </div>
            <div v-else v-for="notificacion in notificaciones" :key="notificacion.id" 
            :class="['px-4 py-2 rounded',!notificacion.read ? 'bg-blue-200' :'']" @click="()=>{ if(!notificacion.read){notificacion.read=true; countnotificaciones--;readnotificacion(notificacion.id)}}">
                <div class="flex justify-between items-center w-full cursor-pointer">
                    <h2 class="font-bold text-[1rem]">{{notificacion.title }}</h2>
                    <span class="text-[0.5rem]">{{ notificacion.created_at }}</span>
                </div>
                <p class="text-sm">{{notificacion.body }}</p>
            </div>
            <div  v-if="hasmore && notificaciones.length!==0" class="text-center mt-2">
                <button @click="()=>{shownotifications += 5; getnotificaciones()}" class="text-blue-500 hover:underline">Ver más</button>
            </div>
        </div>
        </template>
    </Dropdown>
    </div>
</template>