<!-- ModalExample.vue -->
<script setup lang="ts">
import BaseModal from '@/components/Zcrat/modals/BasicModal.vue'
import ButtonTogglePYR from '@/components/Zcrat/Inputs/ButtonTogglePYR.vue'
import MyBasicToast from '@/utils/ToastNotificationBasic'
import axios from 'axios'
import { ref, watch,computed } from 'vue' 
import Loading from '@/components/Zcrat/Elements/Loading.vue';
import type { modulosorden } from '@/types/generales'


const props = defineProps<{ show: boolean , id: number | null }>()
const loading = ref<boolean>(true)

const allmodulos =  ref<modulosorden[]>([])
const usermodulos =  ref<string[]>([])
const accion =  ref<string>("Obteniendo Modulos Orden")

function closeModal() {
  emit('close')
}
const emit = defineEmits<{
  (e: 'close'): void
}>()
const GetUserModulos=async ()=>{
    try {
        loading.value=true;
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
        loading.value=false
    }
}
const ToggleModulo=async(modulo_id:number)=>{
    try {
        loading.value=true;
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
        loading.value=false
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

</script>

<template>
  <BaseModal modaltitle="Administrar Modulos Por Usuario" :position="'center'" :show="show" @close="closeModal">
    <Loading v-if="loading" :text="accion"/>
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
