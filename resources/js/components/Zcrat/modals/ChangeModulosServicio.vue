<!-- ModalExample.vue -->
<script setup lang="ts">
import BaseModal from '@/components/Zcrat/modals/BasicModal.vue'
import ButtonTogglePYR from '@/components/Zcrat/Inputs/ButtonTogglePYR.vue'
import MyBasicToast from '@/utils/ToastNotificationBasic'
import axios from 'axios'
import { ref, watch,computed } from 'vue' 
import Loanding from '@/components/Zcrat/Elements/Loanding.vue';
import type { modulosorden } from '@/types/generales'


const props = defineProps<{ show: boolean , id: number | null }>()
const loanding = ref<boolean>(true)

const allmodulos =  ref<modulosorden[]>([])
const usermodulos =  ref<string[]>([])
const accion =  ref<string>("Obteniendo Modulos Orden")

const emit = defineEmits(['update:show'])

const updateVisibility = (val: boolean) => {
  emit('update:show', val)
}
const GetUserModulos=async ()=>{
    try {
        loanding.value=true;
        accion.value="Obteniendo Roles Y Permisos";
        const response = await axios.get(route('get.modulos.user'),
         { params: { id: props.id } }
        )
        allmodulos.value=response.data.allmodulos;
        usermodulos.value=response.data.usermodulos;
    } catch (error : any) {
        if (error.response?.status === 500) {
            MyBasicToast.error(error.response.data.message || 'Error del servidor')
        } else {
            console.error('Error:', error)
        }
    }finally{
        loanding.value=false
    }
}
const ToggleModulo=async(modulo_id:number)=>{
    try {
        loanding.value=true;
        accion.value="Actualizando Modulos Vistos";
        const response = await axios.post(route('toggle.modulo'),
          {user: props.id, modulo: modulo_id }
        )
        allmodulos.value = [...response.data.allmodulos];
        usermodulos.value = [...response.data.usermodulos];
    } catch (error : any) {
        if (error.response?.status) {
            MyBasicToast.error(error.response.data.message || 'Error del servidor')
        } else {
            console.error('Error:', error)
        }
    }finally{
        loanding.value=false
    }
}
watch(
  () => props.show,
  (newVal) => {
    if (newVal) {
      GetUserModulos()
    }
  }
)
const show = computed({
  get: () => props.show,
  set: (value: boolean) => emit('update:show', value)
})
</script>

<template>
  <BaseModal modaltitle="Administrar Modulos Por Usuario" :position="'center'" v-model:modelValue="show" @update:modelValue="updateVisibility" >
    <Loanding v-if="loanding" :text="accion"/>
     <div v-else class="p-2 gap-2 grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 w-full sm:w-[auto] border-solid border-4 rounded border-blue-300">
        <template v-for="modulo in allmodulos" :key="modulo.id">
          <ButtonTogglePYR 
            :isnew="!usermodulos.includes(modulo.id.toString())" 
            :text="modulo.descripcion"  
            @toggle="()=>{ToggleModulo(modulo.id)}"
          />
        </template>
      </div>
  </BaseModal>
</template>
